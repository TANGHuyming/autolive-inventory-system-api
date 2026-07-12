<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

Route::prefix("auth")->group(function () {
    Route::post("register", [AuthController::class, "register"]);
    Route::post("login", [AuthController::class, "login"])->middleware("throttle:login");

    Route::middleware(["auth:sanctum"])->group(function () {
        Route::get("me", [AuthController::class, "me"]);
        Route::get("logout", [AuthController::class, "logout"]);
    });
});

Route::middleware(["auth:sanctum", "throttle:api"])->group(function () {
    // Inventory endpoints
    Route::get("inventories", [InventoryController::class, "index"]);
    Route::get("inventories/{inventory}", [InventoryController::class, "show"]);
    Route::post("inventories", [InventoryController::class, "store"]);
    Route::post("inventories/{inventory}", [InventoryController::class, "update"]);
    Route::delete("inventories/{inventory}", [InventoryController::class, "destroy"]);

    // Transaction endpoints
    Route::apiResource('transactions', TransactionController::class);

    // Employee endpoints
    Route::get("employees", [EmployeeController::class, "index"]);
    Route::get("employees/{employee}", [EmployeeController::class, "show"]);
    Route::post("employees/{employee}", [EmployeeController::class, "update"]);
    Route::delete("employees/{employee}", [EmployeeController::class, "destroy"]);

    // Warehouse endpoints
    Route::apiResource('warehouses', WarehouseController::class);

    // Role endpoints
    Route::apiResource('roles', RoleController::class);
});
