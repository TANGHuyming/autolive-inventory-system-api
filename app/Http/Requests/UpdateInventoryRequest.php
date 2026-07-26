<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
        $inventory = $this->route('inventory');

        return [
            'nameEn' => ['required', 'string', 'max:255'],
            'nameKh' => ['nullable', 'string', 'max:255'],
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'yearRange' => ['required', 'array', 'min:1'],
            'yearRange.*' => ['required', 'digits:4'],
            'code' => ['required', 'string', "unique:inventories,code,{$inventory->id}", 'max:50'],
            "item_image" => "nullable|file|max:5120|mimes:jpg,jpeg,png,avif",
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            "shelf" => "string|required|max:255",
            "bay" => "string|required|max:100",
            "warehouse" => "string|required|max:100",
            "method" => "string|in:PUT,PATCH|required",
        ];
    }
}
