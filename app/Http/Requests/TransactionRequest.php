<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            "inventory_ids" => "required|array",
            "employee_id" => "required|string|max:255",
            "warehouse_id" => "required|string|max:255",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "telephone" => "required|string|max:15",
            "transaction_date" => "required|date",
        ];
    }
}
