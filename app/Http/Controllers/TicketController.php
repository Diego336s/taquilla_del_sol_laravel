<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index()
    {
        $ticket = Ticket::all();
        return response()->json($ticket, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'evento_id'    => 'required|integer|exists:eventos,id',
            'cliente_id'   => 'required|integer|exists:clientes,id',
            'precio'       => 'required|numeric|min:0',
            'estado'       => 'required|in:pendiente,comprado,cancelado',
            'fecha_compra' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validate();
        $ticket = Ticket::create($validator_datos);

        return response()->json([
            'success' => true,
            'message' => "Ticket generado correctamente",
            'data' => $ticket,
        ], 201);
    }

    public function show(string $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }
        return response()->json($ticket);
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }
        $validator = Validator::make($request->all(), [
            'evento_id'    => 'integer|exists:eventos,id',
            'cliente_id'   => 'integer|exists:clientes,id',
            'tipo'         => 'in:general,vip,estudiante',
            'precio'       => 'numeric|min:0',
            'estado'       => 'in:reservado,comprado,cancelado',
            'fecha_compra' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ticket->update($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Ticket actualizado correctamente',
            'data' => $ticket
        ], 200);
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket eliminado correctamente'], 200);
    }


    public function informacionTicket($id)
    {
        $data = DB::table('tickets')

            // Cliente
            ->join('clientes', 'clientes.id', '=', 'tickets.cliente_id')

            // TABLA RESERVAS (la clave importante)
            ->join('reserva_asientos', 'reserva_asientos.ticket_id', '=', 'tickets.id')

            // Asientos del evento reservados
            ->join('asientos_eventos', 'asientos_eventos.id', '=', 'reserva_asientos.asiento_evento_id')

            // Información del asiento
            ->join('asientos', 'asientos.id', '=', 'asientos_eventos.asiento_id')

            // Ubicación del asiento (VIP, General, Palco, etc)
            ->join('ubicacion_asientos', 'ubicacion_asientos.id', '=', 'asientos.ubicacion_id')

            // Precios asociados al asiento para ese evento
            ->join('precios_eventos', 'precios_eventos.id', '=', 'asientos_eventos.precio_id')
            //Evento asociado al ticket
            ->join("eventos", "eventos.id", "=", "tickets.evento_id")
            ->select(
                // TICKET
                'tickets.id as ticket_id',
                'tickets.precio as total_pagado',
                'tickets.fecha_compra',
                'tickets.estado',

                //Evento
                "eventos.titulo",
                "eventos.fecha as fecha_evento",
                "eventos.hora_inicio",
                "eventos.hora_final",

                // CLIENTE
                'clientes.nombre',
                'clientes.apellido',
                'clientes.correo',
                'clientes.documento',
                'clientes.telefono',

                // ASIENTO
                'asientos.fila',
                'asientos.numero',

                // UBICACION
                'ubicacion_asientos.ubicacion',

                // PRECIO UNITARIO
                'precios_eventos.precio as precio_asiento'
            )
            ->where('tickets.id', $id)
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "El ticket no existe"
            ], 404);
        }

        // --- AGRUPAR ---
        $ticket = $data->first();

        $asientos = $data->map(function ($item) {
            return [
                "fila" => $item->fila,
                "numero" => $item->numero,
                "ubicacion" => $item->ubicacion,
                "precio" => $item->precio_asiento,
            ];
        });

        return response()->json([
            "success" => true,
            "ticket" => [
                "ticket_id"     => $ticket->ticket_id,
                "total_pagado"  => $ticket->total_pagado,
                "fecha_compra"  => $ticket->fecha_compra,
                "estado"        => $ticket->estado,
                "evento" => [
                    "titulo" => $ticket->titulo,
                    "fecha_evento" => $ticket->fecha_evento,
                    "hora_inicio" => $ticket->hora_inicio,
                    "hora_final" => $ticket->hora_final,
                ],
                "cliente" => [
                    "nombre"     => $ticket->nombre,
                    "apellido"   => $ticket->apellido,
                    "correo"     => $ticket->correo,
                    "documento"  => $ticket->documento,
                    "telefono"   => $ticket->telefono,
                ],
                "asientos"      => $asientos
            ]
        ]);
    }
}
