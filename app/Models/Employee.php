<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Employee extends Model
{
    //
    protected $fillable = ["first_name", "last_name", "email", "telephone", "password"];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
