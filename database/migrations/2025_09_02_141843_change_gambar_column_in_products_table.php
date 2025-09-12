<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY gambar LONGBLOB NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY gambar BLOB NULL');
    }
};
