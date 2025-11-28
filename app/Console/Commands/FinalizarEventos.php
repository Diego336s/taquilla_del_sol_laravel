<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FinalizarEventos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finalizar-eventos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para cambiar el estado de los eventos al pasar la fecha del evento';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $hoy = Carbon::now('America/Bogota')->toDateString();


        $hoy = Carbon::now('America/Bogota');

        // 1. Obtener eventos activos cuya fecha ya pasó
        $eventos = DB::table('eventos')
            ->where('estado', 'activo')
            ->where('fecha', '<', $hoy)
            ->get();

        foreach ($eventos as $evento) {

            // === Obtener tickets comprados ===
            $ticketsIds = DB::table('tickets')
                ->where('evento_id', $evento->id)
                ->where('estado', 'comprado')
                ->pluck('id');

            // === Asientos vendidos ===
            DB::table('reserva_asientos')
                ->whereIn('ticket_id', $ticketsIds)
                ->count();

            // === Total recaudado del evento ===
            $totalRecaudado = DB::table('tickets')
                ->whereIn('id', $ticketsIds)
                ->sum('precio');

            // === Calcular porcentajes ===
            $empresa = $totalRecaudado * 0.90;
            $teatro  = $totalRecaudado * 0.10;

            // === Actualizar evento ===
            DB::table('eventos')
                ->where('id', $evento->id)
                ->update([
                    'estado' => 'finalizado',
                    'recaudo_empresa' => $empresa,
                    'recaudo_teatro' => $teatro,
                    'updated_at' => $hoy
                ]);
        }


        return Command::SUCCESS;
    }
}
