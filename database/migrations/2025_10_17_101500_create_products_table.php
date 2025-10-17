<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('nama', 50);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2);
            $table->enum('product_type', ['pin','heart','sticker'])->nullable();
            $table->string('kategori', 20)->nullable();
            $table->binary('gambar')->nullable();
            $table->enum('status', ['active','inactive','out_of_stock'])->default('active');
            $table->enum('stock_flag', ['normal','need_restock','low','critical'])->default('normal');
            $table->timestamps(); // otomatis membuat created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
