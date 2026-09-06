<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends Controller
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
            "limit" => $request->input("limit", $this->PAGE_SIZE),
            "page" => $request->input("page", $this->PAGE),
            "first_name" => $request->input("first_name"),
            "last_name" => $request->input("last_name"),
            "email" => $request->input("email"),
            "telephone" => $request->input("telephone"),
        ];

        $cacheKey = buildCacheKeyFromQuery("employees", $request->query());

        try {
            $employees = Cache::tags(["employees"])->remember($cacheKey, 60, function () use ($data) {
                $query = Employee::search($data["searchQuery"])
                    ->query(function ($query) use ($data) {
                        return $query
                        ->with(['role']);
                    });

                $paginated = $query->latest()->paginate($data["limit"]);

                return [
                    "success" => true,
                    "data" => EmployeeResource::collection($paginated),
                    "meta" => [
                        "pagination" => [
                            "total_pages" => ceil($paginated->total() / $data["limit"]),
                            "current_page" => $paginated->currentPage(),
                            "limit" => $paginated->perPage(),
                        ],
                    ],
                    "message" => "Employees retrieved successfully",
                ];
            });

            return response()->json($employees);
            //
            // $query = Employee::search($data["searchQuery"])
            //     ->query(function ($query) use ($data) {
            //         return $query
            //         ->with(['role']);
            //     });
            //
            // $paginated = $query->latest()->paginate($data["limit"]);
            //
            // return response()->json([
            //     "success" => true,
            //     "data" => EmployeeResource::collection($paginated),
            //     "meta" => [
            //         "pagination" => [
            //             "total_pages" => ceil($paginated->total() / $data["limit"]),
            //             "current_page" => $paginated->currentPage(),
            //             "limit" => $paginated->perPage(),
            //         ],
            //     ],
            //     "message" => "Employees retrieved successfully",
            // ]);
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
    public function show(Employee $employee)
    {
        try {
            $employee->load(['role', 'employeeDocuments', 'transactions']);
            return response()->json([
                "success" => true,
                "data" => new EmployeeResource($employee),
                "message" => "Employee details retrieved successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Internal server error",
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $validated = $request->validated();
            if (!empty($validated["password"])) {
                $validated["password"] = Hash::make($validated["password"]);
            }

            if (!array_key_exists("method", $validated)) {
                throw new \Exception("method field must be specified in request");
            }

            if (!in_array($validated["method"], ["PUT", "PATCH"])) {
                throw new \Exception("Invalid method used. Set method to PUT OR PATCH");
            }

            $updatedEmployee = DB::transaction(function () use ($employee, $validated) {
                if (!empty($validated["avatar"])) {
                    $avatar_path = Storage::disk('public')->putFile("avatars", $validated["avatar"]);
                    EmployeeDocument::updateOrCreate([
                        "document_type" => "avatar",
                    ], [
                        "employee_id" => $employee->id,
                        "file_original_name" => $validated["avatar"]->getClientOriginalName(),
                        "file_mime_type" => $validated["avatar"]->getMimeType(),
                        "file_path" => $avatar_path,
                        "file_size" => $validated["avatar"]->getSize(),
                        "document_type" => "avatar",
                        "status" => "pending",
                    ]);
                }

                $employee->update(
                    $validated,
                );

                Cache::tags(["employees"])->flush();
                return $employee;
            });

            $updatedEmployee->refresh();
            $updatedEmployee->load(['role', 'employeeDocuments']);

            return response()->json([
                "success" => true,
                "data" => new EmployeeResource($updatedEmployee),
                "message" => "Employee updated successfully",
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
    public function destroy(Employee $employee)
    {
        try {
            DB::transaction(function () use ($employee) {
                $employee->employeeDocuments()->delete();
                $employee->delete();
                Cache::tags(["employees"])->flush();
            });

            return response()->json([
                "success" => true,
                "data" => [],
                "message" => "Employee deleted successfully",
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
