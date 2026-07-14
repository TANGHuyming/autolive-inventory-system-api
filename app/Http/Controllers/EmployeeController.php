<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\EmployeeDocument;
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
            "searchQuery" => $request->input("searchQuery"),
            "pageSize" => $request->input("pageSize", $this->PAGE_SIZE),
            "page" => $request->input("page", $this->PAGE),
            "first_name" => $request->input("first_name"),
            "last_name" => $request->input("last_name"),
            "email" => $request->input("email"),
            "telephone" => $request->input("telephone"),
        ];

        try {
            $query = Employee::search($data["searchQuery"])
                ->query(function ($query) use ($data) {
                    return $query
                    ->with(['role']);
                });

            $employees = $query->latest()->paginate($data["pageSize"]);

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
        try {
            $validated = $request->validated();
            $validated["password"] = Hash::make($validated["password"]);

            if (!array_key_exists("method", $validated)) {
                throw new \Exception("method field must be specified in request");
            }

            if (!in_array($validated["method"], ["PUT", "PATCH"])) {
                throw new \Exception("Invalid method used. Set method to PUT OR PATCH");
            }

            $updatedEmployee = DB::transaction(function () use ($employee, $validated) {
                if (!empty($validated["avatar"])) {
                    $employee_avatar = $employee->employeeDocuments()->where("document_type", "avatar")->first();
                    $avatar_path = Storage::disk('public')->putFile("avatars", $validated["avatar"]);
                    $employee_avatar->update([
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
