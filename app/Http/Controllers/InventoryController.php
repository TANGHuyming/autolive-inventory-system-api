<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResource;
use App\Models\InventoryDocument;
use Illuminate\Http\Request;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\Year;
use App\Models\Shelf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            "searchQuery" => $request->input("searchQuery"),
            'page'         => $request->query('page', $this->PAGE),
            'pageSize'     => $request->query('pageSize', $this->PAGE_SIZE),
            'nameEn'       => $request->query('nameEn'),
            'make'         => $request->query('make'),
            'model'        => $request->query('model'),
            'year'         => $request->query('year'),
        ];

        try {
            $query = Inventory::search($data["searchQuery"])
                ->query(function ($query) use ($data) {
                    return $query
                    ->with(['shelves.bay.warehouse', 'inventoryDocuments', 'years.carModel.make', 'transactions.employee']);
                });

            $inventories = $query
                ->latest()
                ->paginate($data["pageSize"]);

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

            $createdInventory = DB::transaction(function () use ($validated) {
                $item_image_path = Storage::disk("public")->putFile("items", $validated["item_image"]);
                $newItem = Inventory::create(
                    $validated
                );

                $query = Year::query()
                    ->with(["carModel.make"])
                    ->when(!empty($validated["year"]), function ($yearQuery) use ($validated) {
                        return $yearQuery
                            ->where("year", $validated["year"])
                            ->whereHas(
                                "carModel",
                                function ($modelQuery) use ($validated) {
                                    return $modelQuery->where("name", $validated["model"]);
                                }
                            )->whereHas(
                                "carModel.make",
                                function ($makeQuery) use ($validated) {
                                    return $makeQuery->where("name", $validated["make"]);
                                }
                            );
                    });

                $year = $query->first();

                $newItem->years()->attach($year->id);

                $query = Shelf::query()
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

                $shelf = $query->first();

                $newItem->shelves()->attach($shelf->id, [
                    "stock_quantity" => $validated["stock_quantity"],
                ]);

                InventoryDocument::create([
                    "inventory_id" => $newItem->id,
                    "file_original_name" => $validated["item_image"]->getClientOriginalName(),
                    "file_mime_type" => $validated["item_image"]->getMimeType(),
                    "file_size" => $validated["item_image"]->getSize(),
                    "file_path" => $item_image_path,
                    "document_type" => "image",
                    "status" => "pending",
                ]);

                return $newItem;
            });

            $createdInventory->load(['shelves.bay.warehouse', 'inventoryDocuments', 'years.carModel.make']);
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
    public function update(Inventory $inventory, InventoryRequest $request)
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

                $query = Year::query()
                    ->with(["carModel.make"])
                    ->when(!empty($validated["year"]), function ($yearQuery) use ($validated) {
                        return $yearQuery
                            ->where("year", $validated["year"])
                            ->whereHas(
                                "carModel",
                                function ($modelQuery) use ($validated) {
                                    return $modelQuery->where("name", $validated["model"]);
                                }
                            )->whereHas(
                                "carModel.make",
                                function ($makeQuery) use ($validated) {
                                    return $makeQuery->where("name", $validated["make"]);
                                }
                            );
                    });

                $year = $query->first();
                $originalYear = $inventory->years()->first()->id;

                $inventory->years()->detach($originalYear);
                $inventory->years()->attach($year->id);

                $query = Shelf::query()
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

                $shelf = $query->first();
                $originalShelf = $inventory->shelves()->first()->id;

                $inventory->shelves()->detach($originalShelf);
                $inventory->shelves()->attach($shelf->id, [
                    "stock_quantity" => $validated["stock_quantity"],
                ]);

                return $inventory;
            });

            $updatedItem->load(["shelves.bay.warehouse", "inventoryDocuments", "years.carModel.make"]);
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
        try {
            $inventory->years()->delete();
            $inventory->transactions()->delete();
            $inventory->inventoryDocuments()->delete();
            $inventory->delete();
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
