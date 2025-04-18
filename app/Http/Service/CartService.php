<?php

namespace App\Http\Service;

use App\Http\Models\Cart;
use Illuminate\Support\Str;

class CartService
{
    public function create(array $data)
    {
        return Cart::create([
            'uuid' => $data['uuid'] ?? (string) Str::uuid(),
            'uuid_user' => $data['uuid_user'],
            'uuid_book' => $data['uuid_book'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function update(string $uuid, array $data)
    {
        $cart = $this->getCartByUuid($uuid);

        $cart->update([
            'uuid_user' => $data['uuid_user'],
            'uuid_book' => $data['uuid_book'],
            'quantity' => $data['quantity'],
        ]);

        return $cart->fresh();
    }

    public function delete(string $uuid)
    {
        $cart = $this->getCartByUuid($uuid);
        return $cart->delete();
    }

    public function updateQuantity(string $uuid, int $quantity)
    {
        $cart = $this->getCartByUuid($uuid);

        $cart->update([
            'quantity' => $quantity,
        ]);

        return $cart->fresh();
    }

    public function removeFromCart($user, $uuid) {

        $cart = Cart::where('uuid_user', $user->uuid)
        ->where('uuid_book', $uuid)
        ->first();

        $cart->delete();

        return $cart->fresh();
    }

    private function getCartByUuid(string $uuid): Cart
    {
        $cart = Cart::where('uuid', $uuid)->first();
    
        return $cart;
    }


}
