<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ShelfResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "item_name_en" => $this->nameEn,
            "item_name_kh" => $this->nameKh,
            "item_make" => $this->make,
            "item_model" => $this->model,
            "item_year" => $this->year,
            "item_code" => $this->code,
            "item_picture_url" => $this->picture_url,
            "shelves" => ShelfResource::collection($this->whenLoaded("shelves")),
            "transactions" => TransactionResource::collection($this->whenLoaded("transactions")),
        ];
    }
}
