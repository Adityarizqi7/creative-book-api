<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class CreatePublisherRequest extends FormRequest
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
            'name' => 'string|required',
            'description' => 'string|nullable',
            'slug' => 'string|unique:publishers,slug',
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
