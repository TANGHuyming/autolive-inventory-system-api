<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\WarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
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
            "limit" => $request->input("limit", $this->PAGE_SIZE),
            "page" => $request->input("page", $this->PAGE),
        ];

        $cacheKey = buildCacheKeyFromQuery("warehouses", $request->query());

        try {
            $warehouses = Cache::tags(["warehouses"])->remember($cacheKey, 60, function () use ($data) {
                $query = Warehouse::query()
                    ->with(['bays.shelves'])
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

                $paginated = $query->latest()->paginate($data["limit"]);
                return [
                    "success" => true,
                    "data" => WarehouseResource::collection($paginated),
                    "meta" => [
                        'pagination' => [
                            "total_pages" => $paginated->lastPage(),
                            "current_page" => $paginated->currentPage(),
                            "limit" => $paginated->perPage(),
                        ],
                    ],
                    "message" => "Warehouses retrieved successfully",
                ];
            });
            return response()->json($warehouses);
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
            $createdWarehouse = DB::transaction(function () use ($validated) {
                $newWarehouse = Warehouse::create($validated);
                Cache::tags(["warehouses"])->flush();
                return $newWarehouse;
            });

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
            $warehouse->load(['bays.shelves']);
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
    public function update(Warehouse $warehouse, UpdateWarehouseRequest $request)
    {
        $validated = $request->validated();

        try {
            $updatedWarehouse = DB::transaction(function () use ($warehouse, $validated) {
                $warehouse->update($validated);
                Cache::tags(["warehouses"])->flush();
                return $warehouse;
            });

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
            DB::transaction(function () use ($warehouse) {
                $warehouse->delete();
                Cache::tags(["warehouses"])->flush();
            });

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
