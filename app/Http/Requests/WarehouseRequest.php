<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
        $warehouse = $this->route("warehouse");

        $codeRule = $warehouse
            ? "unique:warehouses,street,{$warehouse->id}"
            : "unique:warehouses,street";

        return [
            "name" => "required|string|max:255",
            "city" => "string|max:100|nullable",
            "district" => "string|max:100|nullable",
            "commune" => "string|max:100|nullable",
            "village" => "string|max:100|nullable",
            "street" => "string|max:10|nullable|{$codeRule}",
            "house_number" => "string|numeric",
        ];
    }
}
