<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Warehouse;
use App\Models\Transaction;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        "warehouse_id",
        "nameEn",
        "nameKh",
        "make",
        "model",
        "year",
        "quantity",
        "code",
        "shelf",
        "bay",
        "date_acquired",
        "picture_url",
    ];

    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, "inventory_transactions")->withPivot("quantity");
    }
}
