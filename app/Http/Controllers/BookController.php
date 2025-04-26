<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use App\Http\Models\Book;
use App\Http\Models\Promo;
use App\Http\Models\Publisher;
use App\Http\Models\Store;
use App\Http\Models\SubCategory;
use App\Http\Models\Writer;
use Illuminate\Support\Str;
use App\Http\Service\BookService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\CreateBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;

class BookController extends Controller
{
    protected $book_service;

    public function __construct(BookService $book_service)
    {
        $this->book_service = $book_service;
    }

    public function getAllBooks(Book $book) {
        try {
            $books = DB::transaction(function () use ($book) {
                return $book->getAllBooks();
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Buku berhasil didapatkan.',
                'data' => $books,
            ], 200);
            
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data Buku', ['exception' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Buku.'
            ]);
        }
    }

    public function createBook(CreateBookRequest $request) {
        try {

            $url = '';
            $publicId = '';
            $finalPrice = 0;

            $book = DB::transaction(function () use ($request, &$finalPrice, &$url, &$publicId) {

                $validatedData = $request->validated();

                // Metode Penyimpanan Lokal
                // $avatarData = $request->input('image');
                // $image = $this->decodeBase64Image($avatarData);

                // // Menghasilkan nama file untuk gambar baru
                // $name = str_replace(' ', '_', ucwords($validatedData['name']));
                // $avatar_name = "{$name}.{$this->getImageExtension($avatarData)}";

                // // Menyimpan gambar base64 ke disk
                // $avatar_path = Storage::disk('public')->put('images/books/' . $avatar_name, $image);

                // // Jika berhasil menyimpan, simpan path gambar di database
                // if ($avatar_path) {
                //     $validatedData['image'] = 'images/books/' . $avatar_name;
                // }

                // Metode Penyimpanan Cloudinary
                $files = $request->file('images'); 

                $cloudinary = new Cloudinary();

                if ($files) {
                    $uploadResult = $cloudinary->uploadApi()->upload($files->getRealPath(), [
                        'folder'          => 'creativebook/images/books/',
                        'use_filename'    => true,
                        'unique_filename' => false,
                        'overwrite'       => true,
                        'transformation'  => [
                            [
                                'crop'    => 'limit',
                                'width'   => 1000,
                                'quality' => 'auto:best',
                            ]
                        ],
                    ]);
                }

                $uploadedImage = [
                    'url'       => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id'],
                ];

                $url      = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];

                $validatedData['images'] = $uploadedImage;

                $promo = null;
                $discount = 0;
                
                if ($request['uuid_promo']) {
                    $promo = Promo::where('uuid', $request['uuid_promo'])->first();
                    
                    if ($promo) {
                        $discount = $promo->discount;
                    } else {
                        abort(422, 'Promo tidak ditemukan.');
                    }
                }
                
                $originalPrice = $request['original_price'];
                $finalPrice = $originalPrice;
                
                if ($promo) {
                    $finalPrice = $originalPrice - ($originalPrice * ($discount / 100));
                }

                $book_create = $this->book_service->create($validatedData);
                
                $book_create->stores()->attach($request['uuid_store'], [
                    'uuid' => Str::uuid(),
                    'uuid_book' => $book_create->uuid,
                    'original_price' => $originalPrice,
                    'final_price' => $finalPrice,
                    'uuid_promo' => $request->uuid_promo,
                ]);

                return $book_create;

            }); 

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Buku berhasil ditambahkan.',
                'data' => [
                    ...$book->only([
                        'uuid', 'name', 'description', 'image', 'variant_code', 'variant_name',
                        'date_publish', 'page', 'ISBN', 'language', 'long', 'weight', 'width',
                        'uuid_sub_category', 'uuid_writer', 'uuid_publisher', 'slug', 'updated_at',
                    ]),
                    'stores' => [
                        'uuid' => $request->uuid_store,
                        'uuid_promo' => $request->uuid_promo,
                        'original_price' => 'Rp. ' . number_format($request->original_price, 0, ',', '.'),
                        'final_price' => 'Rp. ' . number_format($finalPrice, 0, ',', '.'),
                    ],
                    'images' => [
                        'url_images' => $url,
                        'id_public_images' => $publicId
                    ]
                ],
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Gagal menambah data Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menambah data Buku.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateBook($uuid, UpdateBookRequest $request) {
        try {

            $url = '';
            $publicId = '';
            $finalPrice = 0;

            $book = new Book();
            $book_data = $book->getBookByUuid($uuid);

            if (!$book_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Buku tidak ditemukan.'
                ], 400);
            }

            $sub_category = new SubCategory();
            $sub_category_data = $sub_category->getSubCategoryByUuid($request->uuid_sub_category);

            if (!$sub_category_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Sub Kategori Buku tidak ditemukan.'
                ], 400);
            }
            
            $writer = new Writer();
            $writer_data = $writer->getWriterByUuid($request->uuid_writer);

            if (!$writer_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Penulis Buku tidak ditemukan.'
                ], 400);
            }

            $publisher = new Publisher();
            $publisher_data = $publisher->getPublisherByUuid($request->uuid_publisher);

            if (!$publisher_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Penerbit Buku tidak ditemukan.'
                ], 400);
            }

            $store = new Store();
            $store_data = $store->getStoreByUuid($request->uuid_store);

            if (!$store_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Toko Buku tidak ditemukan.'
                ], 400);
            }

            $promo = new Promo();
            $promo_data = $promo->getPromoByUuid($request->uuid_promo);

            if (!$promo_data) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Diskon Promo Buku tidak ditemukan.'
                ], 400);
            }

            $book = DB::transaction(function () use ($request, $uuid, &$finalPrice) {

                $validatedData = $request->validated();

                $book = new Book();
                $book_detail = $book->getBookByUuid($uuid);

                // Metode Storage Local
                // $avatarData = $request->input('image');
                // $image = $this->decodeBase64Image($avatarData);

                // // Cek apakah image sudah ada, jika ada maka hapus gambar lama
                // if ($book_detail->image) {
                //     Storage::disk('public')->delete($book_detail->image);
                // }

                // // Menghasilkan nama file untuk gambar baru
                // $name = str_replace(' ', '_', ucwords($validatedData['name']));
                // $avatar_name = "{$name}.{$this->getImageExtension($avatarData)}";

                // // Menyimpan gambar base64 ke disk
                // $avatar_path = Storage::disk('public')->put('images/books/' . $avatar_name, $image);

                // // Jika berhasil menyimpan, simpan path gambar di database
                // if ($avatar_path) {
                //     $validatedData['image'] = 'images/books/' . $avatar_name;
                // }

                // Metode Cloudinary
                $files = $request->file('images'); 

                if ($files) {

                    $cloudinary = new \Cloudinary\Cloudinary();
            
                    // 1. Hapus gambar lama dulu
                    if (!empty($book->images) && isset($book->images['public_id'])) {
                        $oldPublicId = $book->images['public_id'];
            
                        try {
                            $cloudinary->uploadApi()->destroy($oldPublicId);
                        } catch (\Exception $e) {
                            Log::error('Gagal menghapus gambar lama di Cloudinary: ' . $e->getMessage());
                        }
                    }
            
                    $uploadResult = $cloudinary->uploadApi()->upload($files->getRealPath(), [
                        'folder'          => 'creativebook/images/books/',
                        'use_filename'    => true,
                        'unique_filename' => false,
                        'overwrite'       => true,
                        'transformation'  => [
                            [
                                'crop'    => 'limit',
                                'width'   => 1000,
                                'quality' => 'auto:best',
                            ]
                        ],
                    ]);
            
                    // 3. Buat array baru
                    $uploadedImage = [
                        'url'       => $uploadResult['secure_url'],
                        'public_id' => $uploadResult['public_id'],
                    ];
            
                    $validated['images'] = $uploadedImage;
                } else {
                    $validated['images'] = $book->images;
                }

                $promo = null;
                $discount = 0;
                
                if ($request['uuid_promo']) {
                    $promo = Promo::where('uuid', $request['uuid_promo'])->first();
                    
                    if (!$promo) {
                        abort(422, 'Promo tidak ditemukan.');
                    }

                    $originalPrice = $request['original_price'];
                    $finalPrice = $originalPrice;
                    
                    if ($promo && now()->between($promo->start_date_flash_sale, $promo->end_date_flash_sale)) {
                        $discount = $promo->discount;
                        $finalPrice = $originalPrice - ($originalPrice * ($discount / 100));
                    } else {
                        abort(422, 'Promo sudah tidak berlaku.');
                    }
                }

                $book_detail->stores()->updateExistingPivot($request->uuid_store, [
                    'uuid' => (string) Str::uuid(),
                    'uuid_book' => $uuid,
                    'original_price' => $request->original_price,
                    'final_price'    => $finalPrice,
                    'uuid_promo'     => $request->uuid_promo,
                    'updated_at'     => now(),
                ]);                

                return $this->book_service->update($uuid, $validatedData);
            });

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Buku berhasil diubah.',
                'data' => [
                    ...$book->only(['uuid','name','description','image','variant_code','variant_name','date_publish','page','ISBN','language','long','weight','width','uuid_sub_category','uuid_writer','uuid_publisher','slug','updated_at']),
                    'stores' => [
                        'uuid' => $request->uuid_store,
                        'promo' => [
                            'name' => $promo_data->name,
                            'slug' => $promo_data->slug,
                            'start_date_flash_sale' => $promo_data->start_date_flash_sale,
                            'end_date_flash_sale' => $promo_data->end_date_flash_sale,
                            'updated_at' => $promo_data->updated_at,
                        ],
                        'original_price' => 'Rp. ' . number_format($request->original_price, 0, ',', '.'),
                        'final_price' => 'Rp. ' . number_format($finalPrice, 0, ',', '.'),
                    ],
                    'images' => [
                        'url_images' => $url,
                        'id_public_images' => $publicId
                    ]
                ] 
            ], 200);

        } catch (\Throwable $e) {
            
            Log::error('Gagal mengubah data Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal mengubah data Buku.',
                'error' => $e->getMessage()
            ], 500);;
        }
    }

    public function deleteBook($uuid) {
        try {

            $book = new Book();
            $book_data = $book->getBookByUuid($uuid);

            if (!$book_data) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Buku tidak ditemukan.'
                ], 404);
            }
            
            DB::transaction(function () use ($uuid, $book_data) {
                
                $cloudinary = new Cloudinary();

                $oldPublicId = $book_data->images['public_id'];
                $cloudinary->uploadApi()->destroy($oldPublicId);


                return $this->book_service->delete($uuid);
            });

            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Buku berhasil di dihapus.',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menghapus data Buku.',
            ], 500);
        }
    }

    // private function decodeBase64Image($base64String) {
    //     $image = str_replace('data:image/png;base64,', '', $base64String);
    //     $image = str_replace('data:image/jpeg;base64,', '', $image);
    //     $image = str_replace('data:image/jpg;base64,', '', $image);
    //     return base64_decode($image);
    // }

    // private function getImageExtension($base64String) {
    //     if (strpos($base64String, 'data:image/jpeg;base64,') === 0) {
    //         return 'jpg';
    //     } elseif (strpos($base64String, 'data:image/png;base64,') === 0) {
    //         return 'png';
    //     } elseif (strpos($base64String, 'data:image/gif;base64,') === 0) {
    //         return 'gif';
    //     }
    //     return 'png';
    // }
}
