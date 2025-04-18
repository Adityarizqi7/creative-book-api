<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'address' => 'sometimes|string|nullable',
            'gender' => 'sometimes|required|in:L,P',
            'date_birth' => 'sometimes|date|nullable',
            'phone' => 'sometimes|string|regex:/^[0-9]{7,13}$/',
            'avatar' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^data:image\/(jpg|jpeg|png);base64,/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'address.string' => 'Alamat harus berupa teks.',

            'gender.required' => 'Jenis kelamin wajib diisi jika ada.',
            'gender.in' => 'Jenis kelamin harus berupa "L" (Laki-laki) atau "P" (Perempuan).',

            'date_birth.date' => 'Tanggal lahir harus dalam format tanggal yang benar.',

            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.regex' => 'Nomor telepon harus terdiri dari 7 sampai 13 angka.',
        ];
    }

}
