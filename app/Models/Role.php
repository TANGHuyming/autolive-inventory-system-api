<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Employee;

class Role extends Model
{
    //
    use HasFactory;

    protected $fillable = ["name", "description"];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, "employee_roles");
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "permissions_roles");
    }
}
