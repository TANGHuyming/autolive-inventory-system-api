<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MakeResource;
use App\Http\Resources\YearResource;

class CarModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "car_model_name" => $this->name,
            "make" => new MakeResource($this->whenLoaded("make")),
            "years" => YearResource::collection($this->whenLoaded("years")),
        ];
    }
}
