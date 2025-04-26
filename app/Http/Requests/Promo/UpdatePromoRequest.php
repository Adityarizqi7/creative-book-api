<?php

namespace App\Http\Requests\Promo;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromoRequest extends FormRequest
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
            'name' => [
                'string',
                'required',
                'sometimes'
            ],
            'discount' => [
                'required',
                'numeric',
                'sometimes'
            ],
            'start_date_flash_sale' => [
                'date_format:Y-m-d H:i:s',
                'required',
                'sometimes',
                'after:now'
            ],
            'end_date_flash_sale' => [
                'date_format:Y-m-d H:i:s',
                'required',
                'sometimes',
                'after:start_date_flash_sale'
            ],
            'slug' => 'string|unique:promos,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name wajib diisi.',
            'name.string' => 'name harus berupa teks.',

            'discount.required' => 'Diskon promo harus diisi.',
            'discount.numeric' => 'Diskon promo harus diisi dengan nilai berupa Angka.',
            
            'start_date_flash_sale.required' => 'Tanggal Mulai Diskon harus diisi.',
            'start_date_flash_sale.date_format' => 'Tanggal Mulai Diskon harus berupa format tanggal yang valid.',

            'end_date_flash_sale.required' => 'Tanggal Selesai Diskon harus diisi.',
            'end_date_flash_sale.date_format' => 'Tanggal Selesai Diskon harus berupa format tanggal yang valid.',
            
            'slug.string' => 'Slug harus berupa teks.',
            'slug.unique' => 'Slug sudah digunakan, silakan pilih yang lain.',
        ];
        
    }
}
