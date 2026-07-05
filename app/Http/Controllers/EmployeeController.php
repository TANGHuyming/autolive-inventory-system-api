<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $data = [
            "first_name" => $request->query("first_name"),
            "last_name" => $request->query("last_name"),
            "email" => $request->query("email"),
            "telephone" => $request->query("telephone"),
        ];

        $query = Employee::query()
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

        $employees = $query->get();
        return response()->json($employees);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();

        $validated["password"] = Hash::make($validated["password"]);

        $employee->update(
            $validated,
        );

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->noContent();
    }
}
