<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->bigIncrements('raw_material_id');
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('satuan', 20);
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->integer('minimum_stock')->default(5);
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps(); // created_at & updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
