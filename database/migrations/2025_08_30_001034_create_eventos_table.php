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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->time("hora_inicio");
            $table->time("hora_final");                            
            $table->enum('estado', ['activo',"pendiente",'cancelado',"finalizado"]);
            $table->foreignId('empresa_id')->constrained('empresas', 'id');
            $table->foreignId('categoria_id')->constrained('categorias', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
