<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\TransactionMade;
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
        $data = [
            "searchQuery" => $request->input("searchQuery"),
            "page" => $request->input("page", $this->PAGE),
            "limit" => $request->input("limit", $this->PAGE_SIZE),
            "first_name" => $request->query("first_name"),
            "last_name" => $request->query("last_name"),
            "telephone" => $request->query("telephone"),
            "transaction_date" => $request->query("transaction_date"),
        ];

        $cacheKey = buildCacheKeyFromQuery("transactions", $request->query());

        try {
            $transactions = Cache::tags(["transactions"])->remember($cacheKey, 60, function () use ($data) {
                $query = Transaction::search($data["searchQuery"])
                    ->query(function ($query) use ($data) {
                        return $query
                            ->with(['inventories', 'employee', 'warehouse'])
                            ->when($data["transaction_date"], function ($q, $v) {
                                return $q->whereBetween("transaction_date", [$v, now()]);
                            });
                    });

                $paginated = $query->latest()->paginate($data["limit"]);
                return [
                    "success" => true,
                    "data" => TransactionResource::collection($paginated),
                    "meta" => [
                        'pagination' => [
                            "total_pages" => $paginated->lastPage(),
                            "current_page" => $paginated->currentPage(),
                            "limit" => $paginated->perPage(),
                        ],
                    ],
                    "message" => "Transactions retrieved successfully",
                ];
            });

            return response()->json($transactions);
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
            $createdTransaction = DB::transaction(function () use ($validated, $request) {
                $syncData = [];

                foreach (collect($validated["items"])->all() as $transactedItem) {
                    // check the availability of each item
                    $item = Inventory::where("id", $transactedItem["inventory_id"])->first();

                    if (!$item) {
                        throw new \Exception("Item does not exist");
                    }

                    $shelf = $item->shelves()->first();
                    $stock_quantity = $shelf->pivot->stock_quantity;

                    if ($stock_quantity < $transactedItem["quantity"]) {
                        throw new \Exception("Quantity is greater than the available stock");
                    }

                    $syncData[$item->id] = ["quantity" => $transactedItem["quantity"]];
                    $item->shelves()->sync([$shelf->id => ["stock_quantity" => $stock_quantity - $transactedItem["quantity"]]]);
                };

                $transaction = Transaction::create(collect($validated)->all());
                $transaction->inventories()->sync($syncData);

                // Testing for now. Can't be put to production unless I buy a domain
                $recipients = [
                    [$request->user()],
                ];

                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->send(new TransactionMade($transaction));
                }

                Cache::tags(["transactions"])->flush();

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
            $transaction = $transaction->load(['inventories', 'warehouse.bays.shelves', 'employee']);
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

    public function summary()
    {
        try {
            $cacheKey = buildCacheKeyFromQuery("transactions_summary", []);
            $summary = Cache::tags(["transactions"])->remember($cacheKey, 60, function () {
                $transaction = fn() => Transaction::query();
                $summary = [
                    "total_count" => $transaction()->count(),
                ];
                return $summary;
            });

            return response()->json([
                "success" => true,
                "data" => $summary,
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
