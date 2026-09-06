<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Auth;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                "cf_turnstile_response" => ["required", new Turnstile()],
                "email" => "email|required|string",
                "password" => "string|required",
            ]);


            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new \Exception("Invalid credentials");
            }

            $user = Auth::user();
            $user->load(['role']);

            return response()->json([
                "success" => true,
                "data" => [
                    "employee" => new EmployeeResource($user),
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
                $employee = Employee::create([
                    "role_id" => 3, // Magic number but 3 is employee role id
                    "first_name" => $validated["first_name"],
                    "last_name" => $validated["last_name"],
                    "email" => $validated["email"],
                    "password" => Hash::make($validated["password"]),
                    "telephone" => $validated["telephone"],
                ]);

                Cache::tags(["employees"])->flush();
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
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

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
            $employee = Auth::user();

            if (!$employee) {
                throw new \Exception("Unauthenticated");
            }

            $employee->load(['role']);

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
