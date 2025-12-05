<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreditApplicationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-Z\s\.]+$/'
            ],
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('credit_applications', 'nik')
            ],
            'vehicle_price' => [
                'required',
                'numeric',
                'min:10000000',
                'max:2000000000'
            ],
            'vehicle_type' => [
                'required',
                'string',
                'in:Motor,Mobil,Truck'
            ],
            'ktp_image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
                'dimensions:min_width=300,min_height=200'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama konsumen harus diisi',
            'customer_name.min' => 'Nama konsumen minimal 3 karakter',
            'customer_name.regex' => 'Nama konsumen hanya boleh berisi huruf dan spasi',
            'nik.required' => 'NIK harus diisi',
            'nik.size' => 'NIK harus tepat 16 digit',
            'nik.regex' => 'NIK hanya boleh berisi angka',
            'nik.unique' => 'NIK sudah pernah mengajukan kredit sebelumnya',
            'vehicle_price.required' => 'Harga kendaraan harus diisi',
            'vehicle_price.min' => 'Harga kendaraan minimal Rp 10.000.000',
            'vehicle_price.max' => 'Harga kendaraan maksimal Rp 2.000.000.000',
            'vehicle_type.required' => 'Jenis kendaraan harus dipilih',
            'vehicle_type.in' => 'Jenis kendaraan tidak valid',
            'ktp_image.required' => 'Foto KTP harus diupload',
            'ktp_image.image' => 'File harus berupa gambar',
            'ktp_image.mimes' => 'Format file harus JPG, JPEG, atau PNG',
            'ktp_image.max' => 'Ukuran file maksimal 2MB',
            'ktp_image.dimensions' => 'Ukuran gambar minimal 300x200 pixel',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => ucwords(strtolower(trim($this->customer_name))),
            'nik' => preg_replace('/[^0-9]/', '', $this->nik),
        ]);
    }
}