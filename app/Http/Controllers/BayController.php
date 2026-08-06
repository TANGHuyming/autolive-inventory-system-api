<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBayRequest;
use App\Http\Requests\CreateBayRequest;
use App\Http\Requests\BayRequest;
use App\Http\Resources\BayResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bay;
use App\Models\Shelf;
use App\Models\Warehouse;

class BayController extends Controller
{
    private $PAGE = 1;
    private $PAGE_SIZE = 10;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $page_size = $request->input("page_size", $this->PAGE_SIZE);
            $query = Bay::query();

            $bays = $query->latest()->paginate($page_size);

            return response()->json([
                'success' => true,
                'data' =>  BayResource::collection($bays),
                'message' => 'Bays retrieved successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'data' => $error->getMessage(),
                'message' => 'Internal server error',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBayRequest $request)
    {
        try {
            $validated = $request->validated();

            $createdBay = DB::transaction(function () use ($validated) {
                $bay = Bay::create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'name' => $validated['name'],
                ]);

                foreach ($validated['shelves'] as $shelf) {
                    Shelf::create([
                        "bay_id" => $bay->id,
                        "name" => $shelf['name'],
                    ]);
                }

                return $bay;
            });

            return response()->json([
                'success' => true,
                'data' => new BayResource($createdBay),
                'message' => 'Bay created successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'data' => $error->getMessage(),
                'message' => 'Internal server error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bay $bay)
    {
        try {
            $bay->load(['shelves']);

            return response()->json([
                'success' => true,
                'data' => new BayResource($bay),
                'message' => 'Bay details retrieved successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                'success' => false,
                'data' => $error->getMessage(),
                'message' => 'Internal server error',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBayRequest $request, Bay $bay)
    {
        try {
            $validated = $request->validated();
            $updatedBay = DB::transaction(function () use ($validated, $bay) {
                $bay->update([
                    'name' => $validated['name'],
                ]);

                $shelfIds = collect($bay->shelves)->map(function ($shelf) {
                    return $shelf->id;
                });

                foreach ($validated['shelves'] as $shelfData) {
                    $shelf = Shelf::find($shelfData['id']);
                    if ($shelf) {
                        $shelf->update([
                            'name' => $shelfData['name'],
                        ]);
                    }

                    $shelfIds = $shelfIds->filter(function ($id) use ($shelfData) {
                        return $id !== $shelfData['id'];
                    });
                }


                foreach ($shelfIds as $shelfId) {
                    $shelf = Shelf::find($shelfId);
                    if ($shelf->inventories()->exists()) {
                        throw new \Exception("Cannot delete shelf with existing inventories.");
                    }
                    $shelf->delete();
                }

                return $bay;
            });

            $updatedBay->load(['shelves']);

            return response()->json([
                'success' => true,
                'data' => new BayResource($updatedBay),
                'message' => 'Bay updated successfully',
            ]);
        } catch (err) {
            return response()->json([
                'success' => false,
                'data' => $error->getMessage(),
                'message' => 'Internal server error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bay $bay)
    {
        try {
            DB::transaction(function () use ($bay) {
                $shelves = $bay->shelves;
                foreach ($shelves as $shelf) {
                    if ($shelf->inventories()->exists()) {
                        throw new \Exception("Cannot delete shelf with existing inventories.");
                    }
                }

                $bay->shelves()->delete();
                $bay->delete();
            });

            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Bay deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
                'message' => 'Internal server error',
            ]);
        }
    }
}
