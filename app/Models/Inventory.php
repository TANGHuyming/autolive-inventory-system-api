<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;
use App\Models\Shelf;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        "nameEn",
        "nameKh",
        "make",
        "model",
        "year",
        "code",
        "picture_url",
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, "inventory_transactions")->withPivot("quantity");
    }

    public function shelves()
    {
        return $this->belongsToMany(Shelf::class, "inventory_shelves")->withPivot("stock_quantity");
    }
}
