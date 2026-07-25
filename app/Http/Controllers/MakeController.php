<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Make;
use App\Http\Resources\MakeResource;

class MakeController extends Controller
{
    private $PAGE_SIZE = 10;

    public function index(Request $request)
    {
        try {
            $pageSize = $request->input('page_size', $this->PAGE_SIZE);
            $query = Make::query()
            ->with(['carModels.years']);

            $makes = $query->orderBy('name', 'ASC')->get();

            return response()->json([
                "success" => true,
                "data" => MakeResource::collection($makes),
                "message" => 'Makes retrieved successfully',
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => error->getMessage(),
                "message" => 'Internal server error',
            ]);
        }
    }
}
