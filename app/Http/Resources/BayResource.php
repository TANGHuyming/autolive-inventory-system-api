<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WarehouseResource;

class BayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "bay_name" => $this->name,
            "warehouse" => new WarehouseResource($this->whenLoaded('warehouse')),
            "shelves" => ShelfResource::collection($this->whenLoaded("shelves")),
        ];
    }
}
