<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\InventoryTransaction;

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
            "quantity" => $request->query("quantity"),
            "quantity_filter" => $request->query("quantity_filter"),
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
            ->when($data["quantity"], function ($q, $v) use ($data) {
                if ($data["quantity_filter"] == "gte") {
                    return $q->where("quantity", ">=", $v);
                } else {
                    return $q->where("quantity", "<=", $v);
                }
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
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            "inventory_ids" => "required|array",
            "employee_id" => "required|string|max:255",
            "warehouse_id" => "required|string|max:255",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "telephone" => "required|string|max:15",
            "transaction_date" => "required|date",
        ]);

        $createdTransaction = Transaction::create($validated);
        $createdTransaction->inventories()->sync($validated["inventory_ids"]);

        return response()->json($createdTransaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
        $items = $transaction->inventories;
        dd($items);
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
        $validated = $request->validate([
            "inventory_ids" => "required|array",
            "employee_id" => "required|string|max:255",
            "warehouse_id" => "required|string|max:255",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "telephone" => "required|string|max:15",
            "transaction_date" => "required|date",
        ]);

        $transaction->update($validated);
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
