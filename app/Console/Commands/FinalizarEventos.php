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

        DB::table('eventos')
            ->where('estado', 'activo')
            ->where('fecha', '<', $hoy)
            ->update([
                'estado' => 'finalizado'
            ]);

        return Command::SUCCESS;
    }
}
