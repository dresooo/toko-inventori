<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('user_id');
            $table->string('full_name', 255);
            $table->string('phone_number', 20);
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('total_amount', 15, 2);
            $table->dateTime('order_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status', ['awaiting_payment', 'paid', 'processing', 'shipped', 'cancelled'])
                  ->default('processing');
            $table->text('shipping_addr');
            $table->binary('custom_gambar')->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->binary('payment_proof')->nullable();

            // Foreign Keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
