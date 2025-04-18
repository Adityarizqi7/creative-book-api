<?php

namespace App\Http\Requests\Borrow;

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
            'uuid_user' => 'required|string|exists:users,uuid',
            'uuid_book' => 'required|string|exists:books,uuid',
            'borrowed_at' => 'required|date',
        ];
    }  

    public function messages(): array
    {
        return [
            'uuid_user.required' => 'UUID user wajib diisi.',
            'uuid_user.string' => 'UUID user harus berupa string.',
            'uuid_user.exists' => 'User dengan UUID yang diberikan tidak ditemukan.',
            'uuid_book.required' => 'UUID buku wajib diisi.',
            'uuid_book.string' => 'UUID buku harus berupa string.',
            'uuid_book.exists' => 'Buku dengan UUID yang diberikan tidak ditemukan.',
            'borrowed_at.required' => 'Tanggal peminjaman wajib diisi.',
            'borrowed_at.date' => 'Tanggal peminjaman harus dalam format yang valid.',
        ];
    }
}
