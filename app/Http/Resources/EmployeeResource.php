<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RoleResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_name" => $this->first_name . ' ' . $this->last_name,
            "employee_email" => $this->email,
            "employee_telephone" => $this->telephone,
            "role" => new RoleResource($this->whenLoaded("role")),
        ];
    }
}
