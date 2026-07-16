<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Warehouse;
use App\Models\Shelf;

class Bay extends Model
{
    use HasFactory;

    protected $touches = ["shelves"];

    protected $fillable = [
        "warehouse_id",
        "name",
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }
}
