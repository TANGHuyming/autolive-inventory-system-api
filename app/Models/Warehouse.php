<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;
use App\Models\Bay;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ["name", "city", "district", "commune", "village", "street", "house_number"];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bays()
    {
        return $this->hasMany(Bay::class);
    }
}
