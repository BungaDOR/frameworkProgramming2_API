<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreProdukRequest extends FormRequest
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
        // STORE
        if ($this->isMethod('post') && !$this->has('_method')) {
            return [
                'kodeBarang' => 'required|string|unique:produk_apis,kodeBarang',
                'namaBarang' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'kategori' => 'required|string',
                'expiredDate' => 'nullable|date',
                'rating' => 'nullable|numeric|min:0|max:5'
            ];
        }

        // UPDATE
        return [
            'kodeBarang' => [
                'sometimes',
                'string',
                Rule::unique('produk_apis', 'kodeBarang')->ignore($this->route('id'))
            ],
            'namaBarang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            //'gambar' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori' => 'nullable|string',
            'expiredDate' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:5'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
