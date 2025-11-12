<?php

namespace App\Http\Controllers;

use App\Models\asientosEventos;
use Illuminate\Support\Facades\DB;

class AsientosEventosController extends Controller
{
    public function asientosPorEventento($id)
    {
        $asientos = DB::table('asientos_eventos as ae')
            ->join('asientos as a', 'ae.asiento_id', '=', 'a.id')
            ->join('ubicacion_asientos as u', 'a.ubicacion_id', '=', 'u.id')
            ->join('precios_eventos as p', 'ae.precio_id', '=', 'p.id')
            ->where('ae.evento_id', $id)
            ->select('a.fila', 'a.numero', 'u.ubicacion', 'p.precio', 'ae.disponible')
            ->get();
        return response()->json([
            "success" => true,
            "asientos" => $asientos
        ]);
    }
}
