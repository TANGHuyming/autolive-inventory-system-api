<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    //
    protected $fillable = ["inventory_id", "transaction_id", "quantity"];
}
