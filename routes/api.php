<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WarehouseController;

Route::apiResource('/inventories', InventoryController::class);
Route::apiResource('/transactions', TransactionController::class);
Route::apiResource('/employees', EmployeeController::class);
Route::apiResource('/warehouses', WarehouseController::class);
