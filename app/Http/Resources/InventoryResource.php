<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ShelfResource;
use App\Http\Resources\BayResource;
use App\Http\Resources\WarehouseResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shelves = $this->whenLoaded("shelves");
        $locations = $shelves->map(function ($shelf) {
            return [
                "warehouse" => new WarehouseResource($shelf->bay->warehouse) ?? null,
                "bay" => new BayResource($shelf->bay) ?? null,
                "shelf" => new ShelfResource($shelf),
                "stock_quantity" => $shelf->pivot->stock_quantity,
            ];
        });

        return [
            "item_name_en" => $this->nameEn,
            "item_name_kh" => $this->nameKh,
            "item_make" => $this->make,
            "item_model" => $this->model,
            "item_year" => $this->year,
            "item_code" => $this->code,
            "item_picture_url" => $this->picture_url,
            "locations" => $locations,
        ];
    }
}
