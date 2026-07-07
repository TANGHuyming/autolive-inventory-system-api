<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "warehouse_name" => $this->name,
            "city" => $this->city,
            "district" => $this->district,
            "commune" => $this->commune,
            "village" => $this->village,
            "street" => $this->street,
            "house_number" => $this->house_number,
        ];
    }
}
