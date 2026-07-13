<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use App\Models\Transaction;
use App\Models\Shelf;
use App\Models\InventoryDocument;

class Inventory extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        "nameEn",
        "nameKh",
        "make",
        "model",
        "year",
        "code",
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, "inventory_transactions")->withPivot("quantity");
    }

    public function shelves()
    {
        return $this->belongsToMany(Shelf::class, "inventory_shelves")->withPivot("stock_quantity");
    }

    public function inventoryDocuments()
    {
        return $this->hasMany(InventoryDocument::class);
    }

    public function searchableAs()
    {
        return 'inventories';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        return $array;
    }
}
