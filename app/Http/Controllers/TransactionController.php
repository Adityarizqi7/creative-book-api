<?php

namespace App\Http\Controllers;

use App\Http\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Service\MidtransService;

class TransactionController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {   
        $this->midtrans = $midtrans;
    }

    public function generateOrderId($userId, $storeId)
    {
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(6));
        return $date . substr($userId, -6) . substr($storeId, -6) . $random;
    }
    

    public function pay(Request $request) {

        $request->validate([
            'uuid_book' => 'required|exists:books,uuid',
            'uuid_store' => 'required|exists:stores,uuid',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $uuidBook = $request->uuid_book;
        $uuidStore = $request->uuid_store;
        $quantity = $request->quantity;

        $book = Book::query()->where('uuid', $uuidBook)->first();

        $grossAmount = OrderService::calculateGrossAmount($uuidBook, $uuidStore, $quantity);

        $orderId = $this->generateOrderId(auth()->guard()->user()->uuid, $uuidStore); ;
    
        $user = auth()->guard()->user();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'name' => $user->name ?? 'Guest',
                'email' => $user->email ?? 'guest@gmail.com',
            ],
            'item_details' => [
                [
                    'id' => $uuidBook,
                    'price' => $grossAmount / $quantity,
                    'quantity' => $quantity,
                    'name' => $book->name,
                ]
            ]
        ];

        $snap = $this->midtrans->createTransaction($params);

        return response()->json([
            'snap_token' => $snap->token,
            'snap_url' => $snap->redirect_url
        ]);
    }

}
