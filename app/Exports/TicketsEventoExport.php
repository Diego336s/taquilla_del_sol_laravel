<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsEventoExport implements FromCollection, WithHeadings
{
    protected $eventoId;

    public function __construct($eventoId)
    {
        $this->eventoId = $eventoId;
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Cliente',
            'Documento',
            'Correo',
            'Teléfono',
            'Evento',
            'Fecha Evento',
            'Hora Inicio',
            'Ubicación',
            'Fila',
            'Número',
            'Precio Asiento',
            'Fecha Compra',
            'Estado'
        ];
    }

    public function collection()
    {
        // === Datos de cada ticket ===
        $tickets = DB::table('tickets')
            ->join('clientes', 'clientes.id', '=', 'tickets.cliente_id')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->join('reserva_asientos', 'reserva_asientos.ticket_id', '=', 'tickets.id')
            ->join('asientos_eventos', 'asientos_eventos.id', '=', 'reserva_asientos.asiento_evento_id')
            ->join('asientos', 'asientos.id', '=', 'asientos_eventos.asiento_id')
            ->join('ubicacion_asientos', 'ubicacion_asientos.id', '=', 'asientos.ubicacion_id')
            ->join('precios_eventos', 'precios_eventos.id', '=', 'asientos_eventos.precio_id')
            ->where('tickets.evento_id', $this->eventoId)
            ->select(
                'tickets.id',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellido) AS cliente"),
                'clientes.documento',
                'clientes.correo',
                'clientes.telefono',
                'eventos.titulo',
                'eventos.fecha',
                'eventos.hora_inicio',
                'ubicacion_asientos.ubicacion',
                'asientos.fila',
                'asientos.numero',
                'precios_eventos.precio AS precio_asiento',
                'tickets.fecha_compra',
                'tickets.estado'
            )
            ->orderBy('tickets.id', 'asc')
            ->get();

        // === TOTAL FINAL DE VENTAS ===
        $totalFinal = $tickets->sum('precio_asiento');

        // === Convertir total en una fila extra ===
        $filaTotal = collect([[
            '', '', '', '', '', '', '', '', '', '', '', '',
            "TOTAL VENDIDO: $totalFinal",
            '',
            ''
        ]]);

        // Retornar tickets + fila total
        return $tickets->concat($filaTotal);
    }
}
