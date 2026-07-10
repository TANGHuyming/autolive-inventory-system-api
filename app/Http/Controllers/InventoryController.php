<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use Illuminate\Http\Request;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    private $PAGE = 1;
    private $PAGE_SIZE = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // search queries
        $data = [
            'page'         => $request->query('page', $this->PAGE),
            'pageSize'     => $request->query('pageSize', $this->PAGE_SIZE),
            'nameEn'       => $request->query('nameEn'),
            'make'         => $request->query('make'),
            'model'        => $request->query('model'),
            'year'         => $request->query('year'),
            'acquired_date' => $request->query('acquired_date'),
        ];
        $pageOffset = ($data["page"] - 1) * $data["pageSize"];

        try {
            // query builder
            $query = Inventory::query()
                ->with([
                    'shelves.bay.warehouse',
                ])
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
                ->when($data["acquired_date"], function ($q, $v) {
                    return $q->whereBetween("acquired_date", [$v, now()])->orderBy("acquired_date", "asc");
                });

            $inventories = $query->limit($data["pageSize"])->skip($pageOffset)->orderBy("created_at", "DESC")->get();
            $inventories = InventoryResource::collection($inventories);

            return response()->json([
                "success" => true,
                "data" => $inventories,
                "message" => "Inventories retrieved successfully",
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
    public function store(InventoryRequest $request)
    {
        $validated = $request->validated();
        try {
            $createdInventory = DB::transaction(function () use ($validated) {
                $newItem = Inventory::create(
                    $validated
                );

                $newItem->shelves()->attach($validated["shelf_id"], [
                    "stock_quantity" => $validated["stock_quantity"],
                ]);

                return $newItem;
            });

            $createdInventory->load(['shelves.bay.warehouse']);
            $createdInventory = new InventoryResource($createdInventory);

            return response()->json([
                "success" => true,
                "data" => $createdInventory,
                "message" => "Item created successfully",
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
    public function show(Inventory $inventory, Request $request)
    {
        $data = [
            "page" => $request->input("page", $this->PAGE),
            "pageSize" => $request->input("pageSize", $this->PAGE_SIZE),
        ];

        try {
            $pageOffset = ($data["page"] - 1) * $data["pageSize"];
            $inventory->load(["shelves.bay.warehouse", 'transactions.employee']);
            $formattedInventory = new InventoryResource($inventory);

            return response()->json([
                "success" => true,
                "data" => $formattedInventory,
                "message" => "Item details queried successfully",
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
    public function update(Inventory $inventory, InventoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $updatedItem = DB::transaction(function () use ($inventory, $validated) {
                $updated = $inventory->update(
                    $validated
                );

                $originalShelf = $inventory->shelves()->first()->id;

                $inventory->shelves()->detach($originalShelf);
                $inventory->shelves()->attach($validated["shelf_id"], [
                    "stock_quantity" => $validated["stock_quantity"],
                ]);

                return $inventory;
            });

            $updatedItem->load(["shelves.bay.warehouse"]);
            $updatedItem = new InventoryResource($updatedItem);

            return response()->json([
                "success" => true,
                "data" => $updatedItem,
                "message" => "Item updated successfully",
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
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response()->noContent();
    }
}
