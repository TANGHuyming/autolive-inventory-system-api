<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Inventory;

class Warehouse extends Model
{
    //
    protected $fillable = ["name", "city", "district", "commune", "village", "street", "house_number"];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
