<?php

namespace App\Http\Controllers;

use App\Http\Models\Book;
use App\Http\Models\Borrow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Service\BorrowService;
use App\Http\Requests\Borrow\CreateBorrowRequest;
use App\Http\Requests\Borrow\ReturnedBookRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BorrowController extends Controller
{

    protected $borrow_service;

    public function __construct(BorrowService $borrow_service)
    {
        return $this->borrow_service = $borrow_service;   
    }

    public function getAllBorrowsBook(Borrow $borrow) {
        try {
            $borrow = DB::transaction(function () use ($borrow) {
                return $borrow->getAllBorrows();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Seluruh Buku yang sedang dipinjam berhasil didapatkan.',
                'data' => $borrow,
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Seluruh Buku yang sedang dipinjam', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Seluruh Buku yang sedang dipinjam.'
            ]);
        }
    }

    public function getAllLateBooks(Borrow $borrow) {
        try {
            $borrow = DB::transaction(function () use ($borrow) {
                return $borrow->getAllLateBooks();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Seluruh Buku yang telat dikembalikan berhasil didapatkan.',
                'data' => $borrow,
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Seluruh Buku yang telat dikembalikan.', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Seluruh Buku yang telat dikembalikan..'
            ]);
        }
    }

    public function getAllReturnedBooks(Borrow $borrow) {
        try {
            $borrow = DB::transaction(function () use ($borrow) {
                return $borrow->getAllReturnedooks();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Seluruh Buku yang sudah dikembalikan berhasil didapatkan.',
                'data' => $borrow,
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Seluruh Buku yang sudah dikembalikan.', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Seluruh Buku yang sudah dikembalikan..'
            ]);
        }
    }

    public function borrowBook(CreateBorrowRequest $request, $uuid) {
        try {

            $book_model = new Book();
            $book = $book_model->getBookByUuid($uuid);
    
            if (!$book) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Buku tidak ditemukan.'
                ], 404);
            }

            $borrow_model = new Borrow();
            $bookWhereWasBorrowed = $borrow_model->getBookWasBorrowed($uuid);

            if ($bookWhereWasBorrowed) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Buku sedang dipinjam oleh Pengguna lain.'
                ], 400);
            }

            $borrowing = DB::transaction(function () use ($request) {

                $validatedData = $request->validated();
    
                return $this->borrow_service->borrowBook($validatedData);
            });
    
            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Buku berhasil dipinjam.',
                'data' => $borrowing->only(['uuid','uuid_user','uuid_book','borrowed_at','due_at','status','fine','returned_at','updated_at']),
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Peminjaman buku gagal.', ['exception' => $e->getMessage()]);
    
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat meminjam buku.',
            ], 500);
        }
    }

    public function returnedBook($uuid, Request $request) {
        
        try {
            
            $validatedData = $request->validate([
                'uuid_user' => 'required|string|exists:users,uuid',
                'uuid_book' => 'required|string|exists:books,uuid',
                'returned_at' => 'required|date',
                'fine_nominal' => 'required|decimal:0,2',
            ], [
                'uuid_user.required' => 'UUID user wajib diisi.',
                'uuid_user.string' => 'UUID user harus berupa string.',
                'uuid_user.exists' => 'User dengan UUID yang diberikan tidak ditemukan.',
                'uuid_book.required' => 'UUID buku wajib diisi.',
                'uuid_book.string' => 'UUID buku harus berupa string.',
                'uuid_book.exists' => 'Buku dengan UUID yang diberikan tidak ditemukan.',
                'returned_at.required' => 'Tanggal peminjaman wajib diisi.',
                'returned_at.date' => 'Tanggal peminjaman harus dalam format yang valid.',
                'fine_nominal.decimal' => 'Format nominal denda tidak valid, maksimal 2 angka di belakang koma.',
            ]);

            $borrow_model = new Borrow();
            $borrow = $borrow_model->getBorrowByUuid($uuid);
            
            if (!$borrow) {
                
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Peminjaman Buku tidak ditemukan.'
                ], 404);

            } else {

                $returnedAt = $request['returned_at'];
                $dueAt = Carbon::parse($borrow->due_at);

                $fine = 0;
                $returnedAtFormatted = Carbon::parse($returnedAt);

                if ($returnedAtFormatted->gt($dueAt)) {
                    $daysLate = $dueAt->diffInDays($returnedAtFormatted);
                    $fine = $daysLate * $request['fine_nominal'];
                    $borrow->status = 'late';
                } else {
                    $fine = 0;
                    $borrow->status = 'returned';
                }
            }
            
            $returnedBook = DB::transaction(function () use ($uuid, $validatedData, $fine, $borrow) {
                return $this->borrow_service->returnedBook($uuid, $fine, $borrow->status, $validatedData);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Buku berhasil dikembalikan.',
                'data' => $returnedBook->only(['uuid','uuid_user','uuid_book','borrowed_at','due_at','status','formatted_fine','returned_at','updated_at']),
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            Log::error('Pengembalian buku gagal.', ['exception' => $e->getMessage()]);
    
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengembalikan buku.',
            ], 500);
        }
    }
}
