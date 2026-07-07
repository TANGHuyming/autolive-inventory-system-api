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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string("nameEn");
            $table->string("nameKh")->nullable();
            $table->string("make");
            $table->string("model");
            $table->year("year");
            $table->string("code")->unique();
            // $table->integer("quantity")->default(0);
            // $table->string("shelf");
            // $table->string("bay");
            $table->string("picture_url")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
