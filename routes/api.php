<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

Route::prefix("auth")->group(function () {
    Route::post("login", [AuthController::class, "login"])->middleware("throttle:login");
    Route::post("register", [AuthController::class, "register"]);

    Route::middleware(["auth:sanctum"])->group(function () {
        Route::get("me", [AuthController::class, "me"]);
        Route::get("logout", [AuthController::class, "logout"]);
    });
});

Route::middleware(["auth:sanctum", "throttle:api"])->group(function () {
    Route::apiResource('inventories', InventoryController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('roles', RoleController::class);
});
