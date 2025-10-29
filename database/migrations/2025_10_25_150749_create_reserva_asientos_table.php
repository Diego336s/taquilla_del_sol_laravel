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
        Schema::create('reserva_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ticket_id")->constrained("tickets", "id");
            $table->foreignId("asiento_id")->constrained("asientos", "id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_asientos');
    }
};
