<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Transaction;

class Inventory extends Model
{
    //
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, "inventory_transactions")->withPivot("quantity");
    }
}
