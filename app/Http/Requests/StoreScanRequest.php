<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScanRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
    return [
        'patient_id' => 'required|exists:patients,id',
        'lung_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Tối đa 5MB
        'doctor_comments' => 'nullable|string|max:500',
    ];
}
}
