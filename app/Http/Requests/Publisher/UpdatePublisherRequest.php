<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublisherRequest extends FormRequest
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
            'slug' => 'string|unique:publishers,slug|sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Penerbit wajib diisi.',
            'name.string' => 'Nama Penerbit harus berupa teks.',

            'desciption.string' => 'Deskripsi harus berupa teks.',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',
        ];
        
    }
}
