<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\InventoryDocument;
use Illuminate\Http\Request;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\Year;
use App\Models\Shelf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class InventoryController extends Controller
{
    public function indexUpToDate(Request $request)
    {
        try {
            $data = [
                'limit' => $request->input('limit'),
            ];
            $query = Inventory::query()
                ->with(['shelves.bay.warehouse', 'years.carModel.make']);

            $inventories = $query
                ->latest()
                ->paginate($data["limit"] ?? 10);

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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // search queries
        $data = [
            "searchQuery" => $request->input("searchQuery"),
            "limit" => $request->input("limit"),
        ];

        try {
            $query = Inventory::search($data["searchQuery"])
                ->query(function ($query) use ($data) {
                    return $query
                    ->with(['shelves.bay.warehouse', 'years.carModel.make']);
                });

            $inventories = $query
                ->latest()
                ->paginate($data["limit"] ?? 10);

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
        try {
            $validated = $request->validated();

            if (!array_key_exists("method", $validated)) {
                throw new \Exception("method field must be specified in request");
            }

            if (!in_array($validated["method"], ["POST"])) {
                throw new \Exception("Invalid method used. Set method to POST");
            }

            $createdInventories = DB::transaction(function () use ($validated) {
                $items = collect($validated["items"])->map(function ($item) {

                    $newItem = Inventory::create([
                        'nameEn' => $item['nameEn'],
                        'nameKh' => $item['nameKh'] ?? null,
                        'code' => $item['code'],
                    ]);

                    collect($item["yearRange"])->each(function ($year) use ($item, $newItem) {
                        $yearQuery = Year::query()
                            ->with(["carModel.make"])
                            ->when(!empty($year), function ($yearQuery) use ($year, $item) {
                                return $yearQuery
                                    ->where("year", $year)
                                    ->whereHas("carModel", function ($modelQuery) use ($item) {
                                        return $modelQuery->where("name", $item["model"]);
                                    })
                                    ->whereHas("carModel.make", function ($makeQuery) use ($item) {
                                        return $makeQuery->where("name", $item["make"]);
                                    });
                            });

                        $queriedYear = $yearQuery->first();
                        $newItem->years()->attach($queriedYear->id);
                    });

                    $shelfQuery = Shelf::query()
                        ->with(["bay.warehouse"])
                        ->when(!empty($item["shelf"]), function ($shelfQuery) use ($item) {
                            return $shelfQuery
                                ->where("name", $item["shelf"])
                                ->whereHas("bay", function ($bayQuery) use ($item) {
                                    return $bayQuery->where("name", $item["bay"]);
                                })
                                ->whereHas("bay.warehouse", function ($warehouseQuery) use ($item) {
                                    return $warehouseQuery->where("name", $item["warehouse"]);
                                });
                        });

                    $shelf = $shelfQuery->first();
                    $newItem->shelves()->attach($shelf->id, [
                        "stock_quantity" => $item["stock_quantity"],
                    ]);

                    if (!empty($item["item_image"])) {
                        $item_image_path = Storage::disk("public")->putFile("items", $item["item_image"]);
                        InventoryDocument::create([
                            "inventory_id" => $newItem->id,
                            "file_original_name" => $item["item_image"]->getClientOriginalName(),
                            "file_mime_type" => $item["item_image"]->getMimeType(),
                            "file_size" => $item["item_image"]->getSize(),
                            "file_path" => $item_image_path,
                            "document_type" => "image",
                            "status" => "pending",
                        ]);
                    }

                    return $newItem;
                });

                return $items;
            });

            $createdInventories = Collection::make($createdInventories);
            $createdInventories->load(['shelves.bay.warehouse', 'inventoryDocuments', 'years.carModel.make']);

            return response()->json([
                "success" => true,
                "data" => InventoryResource::collection($createdInventories),
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
        try {
            $inventory->load(["shelves.bay.warehouse", 'transactions.employee', 'years.carModel.make', 'inventoryDocuments']);
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
    public function update(Inventory $inventory, UpdateInventoryRequest $request)
    {
        try {
            $validated = $request->validated();

            if (!array_key_exists("method", $validated)) {
                throw new \Exception("method field must be specified in request");
            }

            if (!in_array($validated["method"], ["PUT", "PATCH"])) {
                throw new \Exception("Invalid method used. Set method to PUT OR PATCH");
            }

            $updatedItem = DB::transaction(function () use ($inventory, $validated) {
                if (!empty($validated["item_image"])) {
                    $item_image_path = Storage::disk("public")->putFile("items", $validated["item_image"]);
                    $item_image = $inventory->inventoryDocuments()->where("document_type", "image")->first();
                    $payload = [
                        "inventory_id" => $inventory->id,
                        "file_original_name" => $validated["item_image"]->getClientOriginalName(),
                        "file_mime_type" => $validated["item_image"]->getMimeType(),
                        "file_size" => $validated["item_image"]->getSize(),
                        "file_path" => $item_image_path,
                        "document_type" => "image",
                        "status" => "pending",
                    ];

                    if (empty($item_image)) {
                        InventoryDocument::create($payload);
                    } else {
                        $item_image->update($payload);
                    }
                }

                $updated = $inventory->update(
                    $validated
                );

                $newYears = collect($validated["yearRange"])->map(function ($year) use ($validated) {
                    $yearQuery = Year::query()
                        ->with(["carModel.make"])
                        ->when(!empty($year), function ($yearQuery) use ($year, $validated) {
                            return $yearQuery
                                ->where("year", $year)
                                ->whereHas("carModel", function ($modelQuery) use ($validated) {
                                    return $modelQuery->where("name", $validated["model"]);
                                })
                                ->whereHas("carModel.make", function ($makeQuery) use ($validated) {
                                    return $makeQuery->where("name", $validated["make"]);
                                });
                        });

                    $queriedYear = $yearQuery->first();
                    return $queriedYear;
                })->filter(); // filter null values out

                $inventory->years()->sync($newYears);

                $shelfQuery = Shelf::query()
                    ->with(["bay.warehouse"])
                    ->when(!empty($validated["shelf"]), function ($shelfQuery) use ($validated) {
                        return $shelfQuery
                            ->where("name", $validated["shelf"])
                            ->whereHas("bay", function ($bayQuery) use ($validated) {
                                return $bayQuery->where("name", $validated["bay"]);
                            })
                            ->whereHas("bay.warehouse", function ($warehouseQuery) use ($validated) {
                                return $warehouseQuery->where("name", $validated["warehouse"]);
                            });
                    });

                $queriedShelf = $shelfQuery->first();

                $inventory->shelves()->sync($queriedShelf->id, [
                    "stock_quantity" => $validated["stock_quantity"],
                ]);

                return $inventory;
            });

            $updatedItem->load(["shelves.bay.warehouse", "inventoryDocuments", "years.carModel.make"]);

            return response()->json([
                "success" => true,
                "data" => new InventoryResource($updatedItem),
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
        try {
            DB::transaction(function () use ($inventory) {
                $inventory->years()->sync([]);
                $inventory->shelves()->sync([]);
                $inventory->inventoryDocuments()->delete();

                config(['scout.queue' => false]);
                $inventory->delete();
            });

            return response()->json([
                "success" => true,
                "data" => [],
                "message" => "Item deleted successfully",
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
