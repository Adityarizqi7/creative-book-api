<?php

namespace App\Http\Requests\Writer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWriterRequest extends FormRequest
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
            'gender' => [
                'required',
                'in:L,P',
                'sometimes'
            ],
            'slug' => 'string|unique:writers,slug|sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name wajib diisi.',
            'name.string' => 'name harus berupa teks.',

            'gender.required' => 'Jenis Kelamin Penulis harus diisi.',
            'gender.in' => 'Jenis Kelamin Penulis harus diisi dengan nilai L (Laki-laki) atau P (Perempuan).',
        
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',
        ];
        
    }
}
