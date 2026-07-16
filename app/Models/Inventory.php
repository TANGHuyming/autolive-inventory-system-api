<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use App\Models\Transaction;
use App\Models\Shelf;
use App\Models\InventoryDocument;
use App\Models\Year;

class Inventory extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        "nameEn",
        "nameKh",
        "code",
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, "inventory_transactions")->withPivot("quantity");
    }

    public function shelves()
    {
        return $this->belongsToMany(Shelf::class, "inventory_shelves")->withPivot("stock_quantity");
    }

    public function years()
    {
        return $this->belongsToMany(Year::class, "inventory_years");
    }

    public function inventoryDocuments()
    {
        return $this->hasMany(InventoryDocument::class);
    }

    public function searchableAs()
    {
        return 'inventories';
    }

    public function toSearchableArray()
    {
        $this->loadMissing(["shelves.bay.warehouse", "years.carModel.make", "transactions.employee"]);
        $array = $this->toArray();

        $array["shelves"] = $this->shelves->map(function ($shelf) {
            return [
                "name" => $shelf->name,
                "bay" => [
                    "name" => $shelf->bay->name,
                    "warehouse" => $shelf->bay->warehouse->toArray(),
                ],
            ];
        })->toArray();

        $array["years"] = $this->years->map(function ($year) {
            return [
                "year" => $year->year,
                "carModel" => [
                    "name" => $year->carModel->toArray(),
                    "make" => $year->carModel->make->toArray(),
                ],
            ];
        })->toArray();

        $array["transactions"] = $this->transactions->map(function ($t) {
            return [
                "first_name" => $t->first_name,
                "last_name" => $t->last_name,
                "telephone" => $t->telephone,
                "employee" => $t->employee->toArray(),
            ];
        })->toArray();

        return $array;
    }
}
