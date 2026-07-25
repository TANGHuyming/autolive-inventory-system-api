<?php

namespace App\Http\Controllers;

use App\Http\Requests\BayRequest;
use App\Http\Resources\BayResource;
use Illuminate\Http\Request;
use App\Models\Bay;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
