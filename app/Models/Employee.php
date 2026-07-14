<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use App\Models\Transaction;
use App\Models\Role;
use App\Models\EmployeeDocument;

class Employee extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Searchable;

    protected $fillable = ["role_id", "first_name", "last_name", "email", "telephone", "password"];
    protected $hidden = ["password"];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function searchableAs()
    {
        return 'employees';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        return $array;
    }
}
