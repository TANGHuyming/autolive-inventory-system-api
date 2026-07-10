<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WarehouseResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\InventoryResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "warehouse" => new WarehouseResource($this->whenLoaded("warehouse")),
            "approver" => new EmployeeResource($this->whenLoaded("employee")),
            "requester_name" => $this->first_name . ' ' . $this->last_name,
            "telephone" => $this->telephone,
            "transaction_date" => $this->transaction_date,
            "items" => InventoryResource::collection($this->whenLoaded("inventories")),
        ];
    }
}
