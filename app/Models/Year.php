<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarModel;
use App\Models\Inventory;

class Year extends Model
{
    //
    protected $touches = ["inventories"];
    protected $fillable = ["year"];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, "inventory_years");
    }
}
