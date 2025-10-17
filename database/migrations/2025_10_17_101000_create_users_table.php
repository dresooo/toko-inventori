<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('email', 20)->unique();
            $table->string('no_telp', 20);
            $table->text('alamat');
            $table->string('password', 255);
            $table->enum('user_type', ['customer', 'admin']);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
