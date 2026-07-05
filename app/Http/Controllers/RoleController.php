<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $data = [
            "name" => $request->input("name"),
        ];

        $roles = Role::query()
            ->with(["employees"])
            ->when($data["name"], function ($q) use ($data) {
                return $q->where("name", "ILIKE", "%{$data['name']}%");
            });

        return response()->json([
            "success" => true,
            "data" => $roles->get(),
            "message" => "Roles retrieved successfully",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        //
        $validated = $request->validated();

        $newRole = Role::create($validated);

        return response()->json([
            "success" => true,
            "data" => $newRole,
            "message" => "New role created",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
        return response()->json([
            "success" => true,
            "data" => $role,
            "message" => $role->description,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        //
        $validated = $request->validated();

        $role->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
        $role->delete();

        return response()->noContent();
    }
}
