<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId("inventory_id")->constrained("inventories")->onDelete("cascade");
            $table->foreignId("shelf_id")->constrained("shelves")->onDelete("cascade");
            $table->integer("stock_quantity");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_shelves');
    }
};
