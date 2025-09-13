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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id("ticket_id");
            $table->foreignId('evento_id')->constrained('eventos', 'id');
            $table->foreignId('cliente_id')->constrained('clientes', 'id');
            $table->enum("tipo", ["general", "vip", "estudiante"]);
            $table->decimal("precio", 8,2);
            $table->enum("estado",["reservado","comprado","cancelado"]);
            $table->dateTime("fecha_compra");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
