<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viagens', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->id('Id');
            $table->string('destino', 100);
            $table->enum('status', ['solicitada', 'aprovada', 'cancelada'])->default('solicitada');
            $table->string('partida_de', 100);
            $table->string('retorno_de', 100);
            $table->date('data_viagem_ida');
            $table->date('data_viagem_volta');
            $table->unsignedBigInteger('usuario_id');
            $table->dateTime('dataCreated')->nullable();
            $table->dateTime('dataUpdated')->nullable()->useCurrentOnUpdate();

            $table->foreign('usuario_id')->references('Id')->on('usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viagens');
    }
};
