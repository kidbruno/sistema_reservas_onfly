<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->id('Id');
            $table->string('nome', 60);
            $table->integer('idade')->nullable();
            $table->string('email', 100)->unique();
            $table->string('senha', 255);
            $table->enum('status', ['ativo', 'cancelado', 'suspenso'])->default('ativo');
            $table->dateTime('dataCreated')->useCurrent();
            $table->dateTime('dataUpdated')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
