<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->bigIncrements('recipe_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->decimal('quantity_needed', 10, 2);
            $table->string('catatan', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign keys
            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->onDelete('cascade');

            $table->foreign('raw_material_id')
                  ->references('raw_material_id')->on('raw_materials');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
