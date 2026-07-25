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
        $inventory = $this->route('inventory');

        $codeRule = $inventory
            ? "unique:inventories,code,{$inventory->id}"
            : "unique:inventories,code";

        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['array'],
            'items.*.nameEn' => ['required', 'string', 'max:255'],
            'items.*.nameKh' => ['nullable', 'string', 'max:255'],
            'items.*.make' => ['required', 'string', 'max:100'],
            'items.*.model' => ['required', 'string', 'max:100'],
            'items.*.yearRange' => ['required', 'array', 'min:1'],
            'items.*.yearRange.*' => ['required', 'digits:4'],
            'items.*.code' => ['required', 'string', $codeRule, 'max:50'],
            "items.*.item_image" => "file|max:5120|mimes:jpg,jpeg,png,avif",
            'items.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            "items.*.shelf" => "string|required|max:255",
            "items.*.bay" => "string|required|max:100",
            "items.*.warehouse" => "string|required|max:100",
            "method" => "string|in:POST,PUT,PATCH|required",
        ];
    }
}
