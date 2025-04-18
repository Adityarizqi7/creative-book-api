<?php

namespace App\Http\Requests\SubCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubCategoryRequest extends FormRequest
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
            'title' => 'string|required|sometimes',
            'slug' => 'string|unique:sub_categories,slug|sometimes',
            'uuid_category' => 'string|nullable|sometimes',
            'uuid_parent_sub_category' => 'string|nullable|sometimes'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul Sub Kategori wajib diisi.',
            'title.string' => 'Judul Sub Kategori harus berupa teks.',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan gunakan slug lain.',
        
            'uuid_category.string' => 'UUID kategori harus berupa teks.',
            'uuid_category.nullable' => 'UUID kategori boleh kosong.',
        
            'uuid_parent_sub_category.string' => 'UUID subkategori induk harus berupa teks.',
            'uuid_parent_sub_category.nullable' => 'UUID subkategori induk boleh kosong.',
        ];
    }
}
