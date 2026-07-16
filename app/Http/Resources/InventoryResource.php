<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ShelfResource;
use App\Http\Resources\YearResource;

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
            "item_year" => YearResource::collection($this->whenLoaded("years")),
            "item_code" => $this->code,
            "shelves" => ShelfResource::collection($this->whenLoaded("shelves")),
            "transactions" => TransactionResource::collection($this->whenLoaded("transactions")),
            "item_documents" => InventoryDocumentResource::collection($this->whenLoaded("inventoryDocuments")),
        ];
    }
}
