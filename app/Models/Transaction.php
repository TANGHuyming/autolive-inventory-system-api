<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Warehouse;

class Transaction extends Model
{
    //
    protected $fillable = [
        "employee_id",
        "warehouse_id",
        "first_name",
        "last_name",
        "telephone",
        "transaction_date",
    ];

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, "inventory_transactions")->withPivot("quantity");
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
