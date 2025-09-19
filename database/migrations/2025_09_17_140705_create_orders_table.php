<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('user_id'); // FK ke users
            $table->unsignedBigInteger('product_id'); // FK ke products
            $table->integer('quantity');
            $table->decimal('total_amount', 15, 2);
            $table->dateTime('order_date')->useCurrent();
            $table->enum('status', [
                'paid', 'processing', 'shipped','cancelled'
            ])->default('processing');
            $table->text('shipping_addr');
            $table->longBlob('custom_gambar')->nullable(); // gambar custom upload

            // foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
