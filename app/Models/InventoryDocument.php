<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

class InventoryDocument extends Model
{
    protected $fillable = ["inventory_id", "file_original_name", "file_mime_type", "file_size", "document_type", "status"];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
