<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CreateCartRequest extends FormRequest
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
            'uuid_user' => 'required|exists:users,uuid',
            'quantity' => 'required|numeric|min:1',
        ];
    }  

    public function messages(): array
    {
        return [
            'uuid_book.required' => 'Buku wajib dipilih.',
            'uuid_book.exists' => 'Buku tidak ditemukan.',

            'uuid_user.required' => 'Pengguna wajib dipilih.',
            'uuid_user.exists' => 'Pengguna tidak ditemukan.',

            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
        ];
        
    }
}
