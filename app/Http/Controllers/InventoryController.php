<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // search queries
        $data = [
            'nameEn'       => $request->query('nameEn'),
            'make'         => $request->query('make'),
            'model'        => $request->query('model'),
            'year'         => $request->query('year'),
            'shelf'        => $request->query('shelf'),
            'warehouse_id' => $request->query('warehouse_id'),
            'bay'          => $request->query('bay'),
            'acquired_date' => $request->query('acquired_date'),
            'transaction_id' => $request->query('transaction_id'),
        ];

        // query builder
        $query = Inventory::query()
            ->with(
                'transactions'
            )
            ->when($data["nameEn"], function ($q, $v) {
                return $q->where("nameEn", "ILIKE", "%{$v}%");
            })
            ->when($data["make"], function ($q, $v) {
                return $q->where("make", "ILIKE", "%{$v}%");
            })
            ->when($data["model"], function ($q, $v) {
                return $q->where("model", "ILIKE", "%{$v}%");
            })
            ->when($data["year"], function ($q, $v) {
                return $q->where("year", "=", $v);
            })
            ->when($data["warehouse_id"], function ($q, $v) {
                return $q->where("warehouse_id", "=", $v);
            })
            ->when($data["shelf"], function ($q, $v) {
                return $q->where("shelf", "=", strtoupper($v));
            })
            ->when($data["bay"], function ($q, $v) {
                return $q->where("bay", "=", $v);
            })
            ->when($data["acquired_date"], function ($q, $v) {
                return $q->whereBetween("acquired_date", [$v, now()])->orderBy("acquired_date", "asc");
            })
            ->when($data["transaction_id"], function ($q, $v) {
                return $q->whereHas("transactions", function ($q2) use ($v) {
                    return $q2->where("transaction_id", $v);
                });
            });

        $inventory = $query->get();
        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            "warehouse_id" => "string|required|max:255",
            "nameEn" => "string|required|max:255",
            "nameKh" => "string|nullable|max:255",
            "make" => "string|required|max:100",
            "model" => "string|required|max:100",
            "year" => "string|required|numeric|digits:4",
            "code" => "string|required|unique:inventories,code|max:50",
            "quantity" => "integer|min:0",
            "shelf" => "string|required|alpha",
            "bay" => "string|required|numeric|digits:4",
            "picture_url" => "string|nullable",
            "acquired_date" => "date|nullable",
        ]);

        $createdInventory = Inventory::create(
            $validated,
        );

        return response()->json($createdInventory);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return response()->json($inventory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Inventory $inventory, Request $request)
    {
        $validated = $request->validate([
            "warehouse_id" => "string|required|max:255",
            "nameEn" => "string|required|max:255",
            "nameKh" => "string|nullable|max:255",
            "make" => "string|required|max:100",
            "model" => "string|required|max:100",
            "year" => "string|required|numeric|digits:4",
            "code" => "string|required|unique:inventories,code|max:50",
            "quantity" => "integer|min:0",
            "shelf" => "string|required|alpha",
            "bay" => "string|required|numeric|digits:4",
            "picture_url" => "string|nullable",
            "acquired_date" => "date|nullable",
        ]);

        $inventory->update(
            $validated
        );

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        //
        $inventory->delete();
        return response()->noContent();
    }
}
