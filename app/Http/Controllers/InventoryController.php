<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Models\Warehouse;

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
            'shelf'        => $request->query('shelf'),
            'warehouse_id' => $request->query('warehouse_id'),
            'bay'          => $request->query('bay'),
            'acquired_date' => $request->query('acquired_date'),
        ];
        $pageOffset = ($data["page"] - 1) * $data["pageSize"];

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
            });

        $inventory = $query->limit($data["pageSize"])->skip($pageOffset)->orderBy("created_at", "DESC")->get();
        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryRequest $request)
    {
        //Sanitize data
        $validated = $request->validated();
        $item = collect($validated)->map(function ($value, $key) {
            $sanitizedItem = null;
            if (is_string($value)) {
                $sanitizedItem = trim($value);
                $sanitizedItem = strtoupper($value[0]) . strtolower(substr($value, 1, null));
            } else {
                $sanitizedItem = $value;
            }
            return $sanitizedItem;
        });

        $createdInventory = Inventory::create(
            $item->all()
        );

        return response()->json($createdInventory);
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

        $pageOffset = ($data["page"] - 1) * $data["pageSize"];

        $warehouse = Warehouse::query()
            ->where("id", $inventory->warehouse_id)->latest()->first();

        $transactions = $inventory->transactions()->limit($data["pageSize"])->skip($pageOffset)->orderBy("created_at", "DESC")->get();

        $result = collect(["inventory" => $inventory->toArray(), "warehouse" => $warehouse->toArray(), "transactions" => $transactions->toArray()]);

        return response()->json([
            "success" => true,
            "data" => $result,
            "message" => "Item details queried successfully",
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Inventory $inventory, InventoryRequest $request)
    {
        $validated = $request->validated();
        //Sanitize data
        $item = collect($validated)->map(function ($value, $key) {
            $sanitizedItem = null;
            if (is_string($value)) {
                $sanitizedItem = trim($value);
                $sanitizedItem = strtoupper($value[0]) . strtolower(substr($value, 1, null));
            } else {
                $sanitizedItem = $value;
            }
            return $sanitizedItem;
        });

        $inventory->update(
            $item->all()
        );

        return response()->noContent();
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
