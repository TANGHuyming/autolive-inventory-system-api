<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;
use App\Models\CarModel;

class Make extends Model
{
    //
    protected $touches = ["carModels"];
    protected $fillable = ["name", "country_of_origin"];

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, "inventory_makes");
    }

    public function carModels()
    {
        return $this->hasMany(CarModel::class);
    }
}
