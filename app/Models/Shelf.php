<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Bay;
use App\Models\Inventory;

class Shelf extends Model
{
    use HasFactory;

    protected $touches = [
        "inventories",
    ];

    protected $fillable = [
        "bay_id",
        "name",
    ];

    public function bay()
    {
        return $this->belongsTo(Bay::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, "inventory_shelves");
    }
}
