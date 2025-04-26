<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
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
            'name' => 'string|required|unique:books,name',
            'description' => 'string|nullable',
            'slug' => 'string|unique:books,slug',
            'images' => 'mimes:png,jpg,jpeg|max:1536|image',
            'variant_code' => 'string|required',
            'variant_name' => 'string|required',
            'date_publish' => 'date|nullable',
            'page' => 'numeric|required',
            'ISBN' => 'string|nullable',
            'language' => 'string|required',
            'long' => 'numeric|nullable',
            'weight' => 'numeric|nullable',
            'width' => 'numeric|nullable',
            'uuid_sub_category' => 'string|required|exists:sub_categories,uuid',
            'uuid_writer' => 'string|required|exists:writers,uuid',
            'uuid_publisher' => 'string|required|exists:publishers,uuid',

            'uuid_promo' => 'exists:promos,uuid',
            'uuid_store' => 'required|exists:stores,uuid',
            'original_price' => 'required|numeric|min:0',
        ];
    }  

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Toko wajib diisi.',
            'name.string' => 'Nama Toko harus berupa teks.',
            'name.unique' => 'Nama Buku sudah digunakan, silakan pilih yang lain.',
            
            'description.string' => 'Deskripsi harus berupa teks.',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',

            'images.image' => 'Cover Buku harus berupa gambar.',
            'images.mimes' => 'Cover Buku harus memiliki format jpg, jpeg, atau png.',
            'images.max' => 'Ukuran avatar maksimal 1.5MB.',

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
            'uuid_sub_category.exists' => 'Sub Kategori dengan UUID yang diberikan tidak ditemukan.',
            
            'uuid_writer.string' => 'UUID Penulis harus berupa teks.',
            'uuid_writer.required' => 'UUID Penulis wajib diisi.',
            'uuid_writer.exists' => 'Penulis dengan UUID yang diberikan tidak ditemukan.',
            
            'uuid_publisher.string' => 'UUID Penerbit harus berupa teks.',
            'uuid_publisher.required' => 'UUID Penerbit wajib diisi.',
            'uuid_publisher.exists' => 'Penerbit dengan UUID yang diberikan tidak ditemukan.',

            'uuid_store.required' => 'Toko wajib dipilih.',
            'uuid_store.exists' => 'Toko yang dipilih tidak valid atau tidak ditemukan.',

            'original_price.required' => 'Harga asli wajib diisi.',
            'original_price.numeric' => 'Harga asli harus berupa angka.',
            'original_price.min' => 'Harga asli tidak boleh kurang dari 0.',

            'uuid_promo.exists' => 'Promo yang dipilih tidak valid atau tidak ditemukan.'
        ];
        
    }
}
