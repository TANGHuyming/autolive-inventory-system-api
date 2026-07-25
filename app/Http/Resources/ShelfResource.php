<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BayResource;

class ShelfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "shelf_id" => $this->id,
            "shelf_name" => $this->name,
            "stock_quantity" => $this->pivot?->stock_quantity ?? 0,
            "bay" => new BayResource($this->whenLoaded("bay")),
        ];
    }
}
