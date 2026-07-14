<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\Inventory;

class TransactionController extends Controller
{
    private $PAGE = 1;
    private $PAGE_SIZE = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $data = [
            "searchQuery" => $request->input("searchQuery"),
            "page" => $request->input("page", $this->PAGE),
            "pageSize" => $request->input("pageSize", $this->PAGE_SIZE),
            "first_name" => $request->query("first_name"),
            "last_name" => $request->query("last_name"),
            "telephone" => $request->query("telephone"),
            "transaction_date" => $request->query("transaction_date"),
        ];

        try {
            $query = Transaction::search($data["searchQuery"])
                ->query(function ($query) use ($data) {
                    return $query
                        ->with(['inventories', 'employee', 'warehouse'])
                        ->when($data["transaction_date"], function ($q, $v) {
                            return $q->whereBetween("transaction_date", [$v, now()]);
                        });
                });

            $transactions = $query->latest()->paginate($data["pageSize"]);
            return response()->json([
                "success" => true,
                "data" => TransactionResource::collection($transactions),
                "message" => "Transactions retrieved successfully",
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
    public function store(TransactionRequest $request)
    {
        //
        $validated = $request->validated();

        try {
            $createdTransaction = DB::transaction(function () use ($validated) {
                $transaction = Transaction::create(collect($validated)->all());
                $syncData = [];

                foreach (collect($validated["inventory_ids"])->all() as $inventory_id) {
                    // check the availability of each item
                    $item = Inventory::where("id", $inventory_id["inventory_id"])->first();

                    if (!$item) {
                        throw new \Exception("Item does not exist");
                    }

                    $stock_quantity = $item->shelves()->first()->pivot->stock_quantity;

                    if ($stock_quantity < $inventory_id["quantity"]) {
                        throw new \Exception("Quantity is greater than the available stock");
                    }

                    $syncData[$item->id] = ["quantity" => $inventory_id["quantity"]];
                    $item->shelves()->detach($item->id);
                    $item->shelves()->attach($item->id, ["stock_quantity" => $stock_quantity - $inventory_id["quantity"]]);
                };

                $transaction->inventories()->sync($syncData);

                return $transaction;
            });

            $createdTransaction->load(['inventories', 'warehouse', 'employee']);
            $createdTransaction = new TransactionResource($createdTransaction);

            return response()->json([
                "success" => true,
                "data" => $createdTransaction,
                "message" => "Transaction processed successfully",
            ]);
        } catch (\Throwable $error) {
            return [
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction, Request $request)
    {
        $data = [
            "page" => $request->input("page", $this->PAGE),
            "pageSize" => $request->input("pageSize", $this->PAGE_SIZE),
        ];

        $pageOffset = ($data["page"] - 1) * $data["pageSize"];

        try {
            $transaction = $transaction->load(['inventories', 'warehouse', 'employee']);
            return response()->json([
                "success" => true,
                "data" => new TransactionResource($transaction),
                "message" => "Transaction details retrieved successfully",
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
