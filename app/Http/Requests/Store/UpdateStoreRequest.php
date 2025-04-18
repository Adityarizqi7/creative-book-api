<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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
            'city_name' => 'string|required',
            'address' => 'string|nullable|sometimes',
            'slug' => 'string|unique:stores,slug|sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Toko wajib diisi.',
            'name.string' => 'Nama Toko harus berupa teks.',

            'city_name.required' => 'Nama Kota wajib diisi.',
            'city_name.string' => 'Nama Kota harus berupa teks.',

            'address.string' => 'Alamat Toko harus berupa teks.',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',
        ];
        
    }
}
