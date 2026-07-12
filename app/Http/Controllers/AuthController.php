<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Http\Requests\EmployeeRequest;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {

            $validated = $request->validate([
                "email" => "email|required|string",
                "password" => "string|required",
            ]);

            // sanitize email and password
            $email = trim($validated["email"]);
            $password = trim($validated["password"]);

            $employee = Employee::where("email", "=", $email)->first();

            if (!$employee) {
                throw new \Exception("Employee does not exist");
            }

            if (!Hash::check($password, $employee->password)) {
                throw new \Exception("Credentials are invalid");
            }

            $token = $employee->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

            return response()->json([
                "success" => true,
                "data" => [
                    "employee" => new EmployeeResource($employee),
                    "token" => $token,
                ],
                "message" => "Login successful",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    public function register(EmployeeRequest $request)
    {
        try {
            $validated = $request->validated();

            $createdEmployee = DB::transaction(function () use ($validated) {
                $avatar_path = Storage::disk('public')->putFile("avatars", $validated["avatar"]);
                $employee = Employee::create($validated);
                $employee_document = EmployeeDocument::create([
                    "employee_id" => $employee->id,
                    "file_original_name" => $validated["avatar"]->getClientOriginalName(),
                    "file_mime_type" => $validated["avatar"]->getMimeType(),
                    "file_path" => $avatar_path,
                    "file_size" => $validated["avatar"]->getSize(),
                    "document_type" => "avatar",
                    "status" => "pending",
                ]);

                return $employee;
            });

            $createdEmployee->load(["employeeDocuments"]);

            return response()->json([
                "success" => true,
                "data" => new EmployeeResource($createdEmployee),
                "message" => "New employee registered successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                "success" => true,
                "data" => [],
                "message" => "Employee logged out successfully",
            ]);
        } catch (\Throwable $error) {
            return response()->json([
                "success" => false,
                "data" => $error->getMessage(),
                "message" => "Internal server error",
            ]);
        }
    }

    public function me(Request $request)
    {
        try {
            $employee = $request->user();

            if (!$employee) {
                throw new \Exception("Unauthenticated");
            }

            return response()->json([
                "success" => true,
                "data" => new EmployeeResource($employee),
                "message" => "Authenticated",
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
