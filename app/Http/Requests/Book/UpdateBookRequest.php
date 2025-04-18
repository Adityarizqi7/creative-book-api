<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'name' => 'string|required|sometimes',
            'description' => 'string|nullable|sometimes',
            'slug' => 'string|unique:books,slug',
            'image' => [
                'nullable',
                'string',
                'sometimes',
                'regex:/^data:image\/(jpg|jpeg|png);base64,/',
            ],
            'variant_code' => 'string|required|sometimes',
            'variant_name' => 'string|required|sometimes',
            'date_publish' => 'date|nullable|sometimes',
            'page' => 'numeric|required|sometimes',
            'ISBN' => 'string|nullable|sometimes',
            'language' => 'string|required|sometimes',
            'long' => 'numeric|nullable|sometimes',
            'weight' => 'numeric|nullable|sometimes',
            'width' => 'numeric|nullable|sometimes',
            'uuid_sub_category' => 'string|required|sometimes',
            'uuid_writer' => 'string|required|sometimes',
            'uuid_publisher' => 'string|required|sometimes',
            'uuid_promo' => 'exists:promos,uuid|sometimes',
            'uuid_store' => 'required|exists:stores,uuid|sometimes',
            'original_price' => 'required|numeric|min:0|sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Toko wajib diisi.',
            'name.string' => 'Nama Toko harus berupa teks.',
            
            'description.string' => 'Deskripsi harus berupa teks.',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',

            'image.image' => 'Avatar harus berupa gambar.',
            'image.mimes' => 'Avatar harus memiliki format jpg, jpeg, atau png.',
            'image.max' => 'Ukuran avatar maksimal 1.5MB.',

            'variant_code.required' => 'Kode Varian wajib diisi.',
            'variant_code.string' => 'Kode Varian harus berupa teks.',

            'variant_name.required' => 'Kode Varian wajib diisi.',
            'variant_name.string' => 'Kode Varian harus berupa teks.',

            'date_publish.date' => 'Tanggal terit harus berupa format tanggal yang valid.',

            'page.required' => 'Halaman wajib diisi.',
            'page.numeric' => 'Halaman harus berupa angka.',

            'ISBN.string' => 'ISBN harus berupa teks.',

            'language.required' => 'Bahasa wajib diisi.',
            'language.string' => 'Bahasa harus berupa teks.',

            'long.numeric' => 'Panjang Buku harus berupa angka.',
            'weight.numeric' => 'Berat Buku harus berupa angka.',
            'width.numeric' => 'Lebar Buku harus berupa angka.',

            'uuid_sub_category.string' => 'UUID Kategori harus berupa teks.',
            'uuid_sub_category.required' => 'UUID Kategori wajib diisi.',
            
            'uuid_writer.string' => 'UUID Penulis harus berupa teks.',
            'uuid_writer.required' => 'UUID Penulis wajib diisi.',

            'uuid_writer.publisher' => 'UUID Penerbit harus berupa teks.',
            'uuid_writer.publsiher' => 'UUID Penerbit wajib diisi.',

            'uuid_store.required' => 'Toko wajib dipilih.',
            'uuid_store.exists' => 'Toko yang dipilih tidak valid atau tidak ditemukan.',

            'original_price.required' => 'Harga asli wajib diisi.',
            'original_price.numeric' => 'Harga asli harus berupa angka.',
            'original_price.min' => 'Harga asli tidak boleh kurang dari 0.',

            'uuid_promo.exists' => 'Promo yang dipilih tidak valid atau tidak ditemukan.'
        ];
        
    }
}
