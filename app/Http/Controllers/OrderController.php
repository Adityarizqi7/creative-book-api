<?php

namespace App\Http\Controllers;

use App\Http\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function generateOrderId($userId, $storeId)
    {
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(6));
        return $date . substr($userId, -6) . substr($storeId, -6) . $random;
    }

    public function createOrder(Request $request) {

        try {

            $request->validate([
                'uuid_store' => 'required|exists:stores,uuid',
                'uuid_book' => 'required|exists:books,uuid',
                'quantity' => 'required|integer|min:1',
                'final_price' => 'required|integer|min:0',
                'total_price' => 'required|integer|min:0',
                'service_fee' => 'nullable|integer',
            ], [
                'uuid_store.required' => 'Toko tidak ditemukan.',
                'uuid_book.required' => 'Buku tidak ditemukan.',
                'quantity.required' => 'Jumlah harus diisi.',
                'quantity.integer' => 'Jumlah harus berupa angka.',
                'quantity.min' => 'Jumlah minimal adalah 1.',
                'final_price.required' => 'Harga akhir harus diisi.',
                'final_price.integer' => 'Harga akhir harus berupa angka.',
                'final_price.min' => 'Harga akhir minimal adalah 0.',
                'total_price.required' => 'Total harga harus diisi.',
                'total_price.integer' => 'Total harga harus berupa angka.',
                'total_price.min' => 'Total harga minimal adalah 0.',
                'service_fee.integer' => 'Biaya layanan harus berupa angka.',
            ]);
    
            $orderId = $this->generateOrderId(auth()->guard()->user()->uuid, $request->uuid_store);

            $serviceFee = $request->service_fee ?? 0;
    
            $order = Order::create([
                'uuid_user' => auth()->guard()->user()->uuid,
                'uuid_store' => $request->uuid_store,
                'uuid_book' => $request->uuid_book,
                'quantity' => $request->quantity,
                'final_price' => $request->final_price,
                'total_price' => $request->total_price,
                'service_fee' => $serviceFee,
                'order_id' => $orderId,
                'status' => 'pending',
            ]);
    
            return response()->json([
                'code' => 201,
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat data Pemesanan Buku.', ['exception' => $e->getMessage()]);

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal membuat data Pemesanan Buku.'
            ], 500);
        }
    }

    public function updateOrderWithMidtrans(Request $request, $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();

        $order->update([
            'snap_token' => $request->snap_token,
            'snap_url' => $request->snap_url,
        ]);

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'message' => 'Order updated with payment details',
            'data' => $order,
        ]);
    }

    public function updateOrderStatus(Request $request, $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();

        $status = $request->status;
        $order->update([
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order,
        ]);
    }

}
