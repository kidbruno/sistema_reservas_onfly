<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE DATABASE IF NOT EXISTS `sistema_reservas` CHARACTER SET utf8 COLLATE utf8_general_ci");
    }

    public function down(): void
    {
        DB::statement("DROP DATABASE IF EXISTS `sistema_reservas`");
    }
};
