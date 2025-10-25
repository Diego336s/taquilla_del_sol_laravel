<?php

use App\Models\Empresas;
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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();           
            $table->string('nombre_empresa');
            $table->string('nit')->unique();
            $table->string('representante_legal');
            $table->string('documento_representante')->unique();
            $table->string('nombre_contacto');
            $table->string('telefono')->nullable();
            $table->string('correo')->unique();
            $table->string ('clave');
             $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
