<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWasteReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'category_id' => ['required', 'integer', 'exists:waste_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Foto bukti kondisi sampah wajib diunggah.',
            'photos.array' => 'Format upload foto tidak valid.',
            'photos.min' => 'Minimal sertakan 1 foto bukti kondisi sampah.',
            'photos.max' => 'Maksimal 5 foto bukti yang dapat diunggah.',
            'photos.*.image' => 'File yang diunggah harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 10MB.',
            'latitude.required' => 'Koordinat latitude wajib disertakan.',
            'latitude.numeric' => 'Koordinat latitude harus berupa angka desimal.',
            'longitude.required' => 'Koordinat longitude wajib disertakan.',
            'longitude.numeric' => 'Koordinat longitude harus berupa angka desimal.',
            'category_id.required' => 'Pilih kategori sampah yang dilaporkan.',
            'category_id.exists' => 'Kategori sampah yang dipilih tidak valid.',
            'description.max' => 'Deskripsi laporan maksimal 1000 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validasi formulir laporan gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
