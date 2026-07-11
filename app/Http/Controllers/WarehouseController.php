<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    private $PAGE = 1;
    private $PAGE_SIZE = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [
            "name" => $request->input("name"),
            "city" => $request->input("city"),
            "district" => $request->input("district"),
            "commune" => $request->input("commune"),
            "village" => $request->input("village"),
            "street" => $request->input("street"),
            "house_number" => $request->input("house_number"),
        ];
        $page = $request->input("page", $this->PAGE);
        $page_size = $request->input("page_size", $this->PAGE_SIZE);
        $page_offset = ($page - 1) * $page_size;

        try {
            $query = Warehouse::query()
                ->when(!empty($data['name']), function ($query) use ($data) {
                    return $query->where('name', 'ILIKE', $data['name']);
                })
                ->when(!empty($data['city']), function ($query) use ($data) {
                    return $query->where('city', 'ILIKE', $data['city']);
                })
                ->when(!empty($data['district']), function ($query) use ($data) {
                    return $query->where('district', 'ILIKE', $data['district']);
                })
                ->when(!empty($data['commune']), function ($query) use ($data) {
                    return $query->where('commune', 'ILIKE', $data['commune']);
                })
                ->when(!empty($data['village']), function ($query) use ($data) {
                    return $query->where('village', 'ILIKE', $data['village']);
                })
                ->when(!empty($data['street']), function ($query) use ($data) {
                    return $query->where('street', 'ILIKE', $data['street']);
                })
                ->when(!empty($data['house_number']), function ($query) use ($data) {
                    return $query->where('house_number', 'ILIKE', $data['house_number']);
                });

            $warehouses = $query->limit($page_size)->skip($page_offset)->get();
            return response()->json([
                "success" => true,
                "data" => WarehouseResource::collection($warehouses),
                "message" => "Warehouses retrieved successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseRequest $request)
    {
        $validated = $request->validated();

        try {
            $createdWarehouse = Warehouse::create($validated);
            return response()->json([
                "success" => true,
                "data" => new WarehouseResource($createdWarehouse),
                "message" => "Warehouse registered successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        try {
            $warehouse->load(['bays']);
            return response()->json([
                "success" => true,
                "data" => new WarehouseResource($warehouse),
                "message" => "Warehouse details retrieved successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Warehouse $warehouse, WarehouseRequest $request)
    {
        $validated = $request->validated();

        try {
            $warehouse->update($validated);
            $warehouse->refresh();
            $warehouse->load(['bays']);
            return response()->json([
                "success" => true,
                "data" => new WarehouseResource($warehouse),
                "message" => "Warehouse updated successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            $warehouse->delete();
            return response()->json([
                "success" => true,
                "data" => [],
                "message" => "Warehouse deleted successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }
}
