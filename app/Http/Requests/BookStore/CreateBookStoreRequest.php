<?php

namespace App\Http\Requests\BookStore;

use Illuminate\Foundation\Http\FormRequest;

class CreateBorrowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid_book' => 'required|exists:books,uuid',
            'uuid_store' => 'required|exists:stores,uuid',
            'original_price' => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0|lte:original_price',
            'uuid_promo' => 'nullable|exists:promos,uuid',
        ];
    }  

    public function messages(): array
    {
        return [
            'uuid_book.required' => 'Buku wajib dipilih.',
            'uuid_book.exists' => 'Buku yang dipilih tidak valid atau tidak ditemukan.',

            'uuid_store.required' => 'Toko wajib dipilih.',
            'uuid_store.exists' => 'Toko yang dipilih tidak valid atau tidak ditemukan.',

            'original_price.required' => 'Harga asli wajib diisi.',
            'original_price.numeric' => 'Harga asli harus berupa angka.',
            'original_price.min' => 'Harga asli tidak boleh kurang dari 0.',

            'final_price.required' => 'Harga akhir wajib diisi.',
            'final_price.numeric' => 'Harga akhir harus berupa angka.',
            'final_price.min' => 'Harga akhir tidak boleh kurang dari 0.',
            'final_price.lte' => 'Harga akhir tidak boleh lebih besar dari harga asli.',

            'uuid_promo.exists' => 'Promo yang dipilih tidak valid atau tidak ditemukan.'
        ];
    }
}
