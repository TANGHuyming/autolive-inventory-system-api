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
        Schema::create('inventory_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId("inventory_id")->constrained("inventories");
            $table->string("file_original_name");
            $table->string("file_mime_type");
            $table->string("file_size");
            $table->enum("document_type", ["image", "others"]);
            $table->string("file_path")->unique();
            $table->enum("status", ["verified", "pending", "rejected"]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_documents');
    }
};
