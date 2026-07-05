<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $role = $user->roles[0]->name;
        $allowedRoles = ['admin', 'super_admin'];

        if (in_array($role, $allowedRoles)) {
            return true;
        }

        return false;
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
            "name" => ["string", "required", "unique:roles,name", "max:100"],
            "description" => ["string", "nullable"],
        ];
    }
}
