<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            "warehouse_id" => "string|required",
            "nameEn" => "string|required|max:255",
            "nameKh" => "string|nullable|max:255",
            "make" => "string|required|max:100",
            "model" => "string|required|max:100",
            "year" => "string|required|numeric|digits:4",
            "code" => "string|required|unique:inventories,code|max:50",
            "quantity" => "integer|min:0",
            "shelf" => "string|required|alpha",
            "bay" => "string|required|numeric|digits:4",
            "picture_url" => "string|nullable",
        ];
    }
}
