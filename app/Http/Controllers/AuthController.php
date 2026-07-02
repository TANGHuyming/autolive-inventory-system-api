<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $validated = $request->validate([
            "email" => "email|required|string",
            "password" => "string|required",
        ]);

        // sanitize email and password
        $email = trim($validated["email"]);
        $password = trim($validated["password"]);

        $employee = Employee::where("email", "=", $email)->first();

        if (!$employee) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Employee does not exist",
            ]);
        }

        if (!Hash::check($password, $employee->password)) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Invalid password",
            ]);
        }

        $token = $employee->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

        return response()->json([
            "success" => true,
            "data" => [
                "employee" => $employee,
                "token" => $token,
            ], // with token
            "message" => "Login successful",
        ]);
    }

    public function register(Request $request)
    {
        //
        $validated = $request->validate([
            "first_name" => "required|max:255|string",
            "last_name" => "required|max:255|string",
            "email" => "required|max:255|email|string",
            "telephone" => "required|string|max:15",
            "password" => "required|string|max:255",
        ]);

        $employee = Employee::make([
            "first_name" => $validated["first_name"],
            "last_name" => $validated["last_name"],
            "email" => $validated["email"],
            "telephone" => $validated["telephone"],
            "password" => Hash::make($validated["password"]),
        ]);

        // create the record
        $employee->save();

        return response()->json([
            "success" => true,
            "data" => [
                "employee" => $employee,
            ],
            "message" => "New employee registered successfully",
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "success" => true,
            "data" => [],
            "message" => "Employee logged out successfully",
        ]);
    }
}
