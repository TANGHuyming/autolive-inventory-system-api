<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "document_original_name" => $this->file_original_name,
            "document_mime_type" => $this->file_mime_type,
            "document_size" => $this->file_size,
            "document_path" => $this->file_path,
            "document_type" => $this->document_type,
            "document_status" => $this->status,
        ];
    }
}
