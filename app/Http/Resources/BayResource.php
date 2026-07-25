<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WarehouseResource;
use App\Http\Resources\ShelfResource;

class BayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shelfCount = $this->whenLoaded('shelves', fn() => $this->shelves->count());

        return [
            "bay_id" => $this->id,
            "bay_name" => $this->name,
            "warehouse" => new WarehouseResource($this->whenLoaded('warehouse')),
            "shelves" => ShelfResource::collection($this->whenLoaded('shelves')),
            "shelf_count" => $shelfCount,
        ];
    }
}
