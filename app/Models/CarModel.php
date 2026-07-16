<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Make;
use App\Models\Year;

class CarModel extends Model
{
    //
    protected $touches = ["years"];
    protected $fillable = ["make_id", "name"];

    public function years()
    {
        return $this->hasMany(Year::class);
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }
}
