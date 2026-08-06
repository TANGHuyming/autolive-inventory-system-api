<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBayRequest extends FormRequest
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
            'warehouse_id' => ['string', 'required'],
            'name' => 'string|max:10|required',
            'shelves' => ['array', 'required', 'min:1'],
            'shelves.*.name' => ['string', 'min:1', 'max:100', 'required'],
        ];
    }
}
