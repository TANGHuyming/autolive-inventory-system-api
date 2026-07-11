<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transaction;
use App\Models\Role;

class Employee extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    protected $fillable = ["first_name", "last_name", "email", "telephone", "password"];
    protected $hidden = ["password"];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
