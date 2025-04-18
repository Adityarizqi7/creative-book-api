<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends FormRequest
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
            'address' => 'string|nullable',
            'gender' => 'required|in:L,P',
            'date_birth' => 'date|nullable',
            'phone' => 'string|regex:/^[0-9]{7,13}$/',
            'avatar' => [
                'nullable',
                'string',
                'regex:/^data:image\/(jpg|jpeg|png);base64,/',
            ],
        ];
    }

    public function messages(): array {
        return [
            'address.string' => 'Alamat harus berupa teks.',
            
            'gender.required' => 'Jenis kelamin wajib diisi.',
            'gender.in' => 'Jenis kelamin harus diisi dengan nilai L (Laki-laki) atau P (Perempuan).',

            'date_birth.date' => 'Tanggal lahir harus berupa format tanggal yang valid.',

            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.regex' => 'Nomor telepon harus terdiri dari 7 hingga 13 angka.',

            'avatar.image' => 'Avatar harus berupa gambar.',
            'avatar.mimes' => 'Avatar harus memiliki format jpg, jpeg, atau png.',
            'avatar.max' => 'Ukuran avatar maksimal 1.5MB.',
        ];
    }

}
