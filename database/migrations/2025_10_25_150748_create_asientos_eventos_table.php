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
        Schema::create('asientos_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("evento_id")->constrained("eventos","id");
            $table->foreignId("asiento_id")->constrained("asientos","id");
            $table->boolean("disponible");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_eventos');
    }
};
