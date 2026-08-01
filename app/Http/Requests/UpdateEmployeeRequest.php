<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            //
            "first_name" => "required|max:255|string",
            "last_name" => "required|max:255|string",
            "email" => "required|max:255|email|string",
            "telephone" => "required|string|max:20",
            "password" => "nullable|string|max:255",
            "avatar" => ['nullable', 'file', "max:5120", "mimes:jpg,jpeg,png,avif"],
            "method" => ['required', 'string', 'in:PUT,PATCH'],
        ];
    }
}
