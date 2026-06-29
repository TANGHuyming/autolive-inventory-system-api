<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $warehouses = Warehouse::all();
        return response()->json($warehouses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "city" => "string|max:100|nullable",
            "district" => "string|max:100|nullable",
            "commune" => "string|max:100|nullable",
            "village" => "string|max:100|nullable",
            "street" => "string|max:10|nullable|unique:warehouses,street",
            "house_number" => "string|numeric",
        ]);

        $createdWarehouse = Warehouse::create($validated);
        return response()->json($createdWarehouse);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        //
        return response()->json($warehouse);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Warehouse $warehouse, Request $request)
    {
        //
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "city" => "string|max:100|nullable",
            "district" => "string|max:100|nullable",
            "commune" => "string|max:100|nullable",
            "village" => "string|max:100|nullable",
            "street" => "string|max:10|nullable|unique:warehouses,street",
            "house_number" => "string|numeric",
        ]);

        $warehouse->update($validated);
        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        //
        $warehouse->delete();
        return response()->noContent();
    }
}
