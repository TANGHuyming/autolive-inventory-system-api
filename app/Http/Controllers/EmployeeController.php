<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $employees = Employee::all();
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            "first_name" => "required|max:255|string",
            "last_name" => "required|max:255|string",
            "email" => "required|max:255|email|string",
            "telephone" => "required|string|max:15",
            "password" => "required|string|max:255",
        ]);

        // create the record
        $createdEmployee = Employee::create(
            $validated,
        );

        return response()->json($createdEmployee);
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
    public function update(Request $request, Employee $employee)
    {
        //
        $validated = $request->validate([
            "first_name" => "required|max:255|string",
            "last_name" => "required|max:255|string",
            "email" => "required|max:255|email:rfc,dns|string",
            "telephone" => "required|string|max:15",
            "password" => "required|string|max:255",
        ]);


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
        //
        $employee->delete();

        return response()->noContent();
    }
}
