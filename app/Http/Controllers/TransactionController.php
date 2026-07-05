<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\Inventory;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $data = [
            "first_name" => $request->query("first_name"),
            "last_name" => $request->query("last_name"),
            "telephone" => $request->query("telephone"),
            "transaction_date" => $request->query("transaction_date"),
            "inventory_id" => $request->query("inventory_id"),
        ];

        $query = Transaction::query()
            ->with('inventories')
            ->when($data["first_name"], function ($q, $v) {
                return $q->where("first_name", "LIKE", "%{$v}%");
            })
            ->when($data["last_name"], function ($q, $v) {
                return $q->where("last_name", "LIKE", "%{$v}%");
            })
            ->when($data["telephone"], function ($q, $v) {
                return $q->where("telephone", "=", $v);
            })
            ->when($data["transaction_date"], function ($q, $v) {
                return $q->whereBetween("transaction_date", [$v, now()]);
            })
            ->when($data["inventory_id"], function ($q, $v) {
                return $q->whereHas("inventories", function ($q2) use ($v) {
                    return $q2->where("inventory_id", $v);
                });
            });

        $transactions = $query->get();
        return response()->json($transactions);
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
                $syncData = [];

                foreach (collect($validated["inventory_ids"])->all() as $inventory_id) {
                    // check the availability of each item
                    $item = Inventory::where("id", $inventory_id["inventory_id"])->lockForUpdate()->first();

                    if (!$item) {
                        throw new RuntimeException(json_encode([
                            "success" => false,
                            "data" => [],
                            "message" => "Item not found",
                        ]));
                    }

                    if ($item->quantity < $inventory_id["quantity"]) {
                        throw new RuntimeException(json_encode([
                            "success" => false,
                            "data" => [
                                "item_name" => $item->nameEn . $item->nameKh,
                                "quantity" => $inventory_id["quantity"],
                                "available_stock" => "{$item->quantity}",
                            ],
                            "message" => "Quantity is greater than the available stock",
                        ]));
                    }

                    $syncData[$item->id] = ["quantity" => $inventory_id["quantity"]];
                    $item->decrement("quantity", $inventory_id["quantity"]);
                };

                $transaction = Transaction::create(collect($validated)->except("inventory_ids")->all());
                $transaction->inventories()->sync($syncData);

                return $transaction;
            });

            return response()->json([
                "success" => true,
                "data" => $createdTransaction,
                "message" => "Transaction processed successfully",
            ]);
        } catch (RuntimeException $e) {
            return response()->json(json_decode($e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionRequest $request, Transaction $transaction)
    {
        //
        $validated = $request->validated();

        $transaction->update(collect($validated)->all());
        $transaction->inventories()->sync($validated["inventory_ids"]);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
        $transaction->delete();
        return response()->noContent();
    }
}
