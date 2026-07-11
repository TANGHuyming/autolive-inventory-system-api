<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;

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
            "first_name" => $request->input("first_name"),
            "last_name" => $request->input("last_name"),
            "email" => $request->input("email"),
            "telephone" => $request->input("telephone"),
        ];

        $page = $request->input("page");
        $page_size = $request->input("page_size");
        $page_offset = ($page - 1) * $page_size;

        try {
            $query = Employee::query()
                ->with(['role'])
                ->when($data["first_name"], function ($q, $v) {
                    return $q->where("first_name", "ILIKE", "%{$v}%");
                })
                ->when($data["last_name"], function ($q, $v) {
                    return $q->where("last_name", "ILIKE", "%{$v}%");
                })
                ->when($data["email"], function ($q, $v) {
                    return $q->where("email", "ILIKE", "%{$v}%");
                })
                ->when($data["telephone"], function ($q, $v) {
                    return $q->where("telephone", "=", $v);
                });

            $employees = $query->limit($page_size)->skip($page_offset)->get();
            return response()->json([
                "success" => true,
                "data" => EmployeeResource::collection($employees),
                "message" => "Employees retrieved successfully",
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
    public function show(Employee $employee)
    {
        try {
            $employee->load(['role']);
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
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();
        $validated["password"] = Hash::make($validated["password"]);

        try {
            $employee->update(
                $validated,
            );
            $employee->refresh();
            $employee->load(['role']);

            return response()->json([
                "success" => true,
                "data" => new EmployeeResource($employee),
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
            $employee->delete();
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
