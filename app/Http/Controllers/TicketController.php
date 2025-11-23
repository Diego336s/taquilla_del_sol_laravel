<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\Snappy\Facades\SnappyPdf;
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

    public function verificarUsoTickect(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|integer|exists:tickets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación",
                "errors"  => $validator->errors()
            ]);
        }


        $id = $request->ticket_id;
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json([
                "success" => false,
                "message" => "Ticket no encontrado."
            ]);
        }

        // Revisar si ya fue usado
        if ($ticket->usado === true) {
            return response()->json([
                "success" => false,
                "message" => "Este ticket ya ha sido usado."
            ]);
        }

        // Marcar como usado
        $ticket->update([
            "usado" => true
        ]);

        return response()->json([
            "success" => true,
            "message" => "🎭 Ticket válido. ¡Bienvenido al Teatro del Sol!",
            "ticket" => $ticket
        ]);
    }

    public function misTickets($clienteId)
    {

        $tickets = DB::table('tickets')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->where('tickets.cliente_id', $clienteId)
            ->select(
                'tickets.id as ticket_id',
                'tickets.precio',
                'tickets.usado',
                'tickets.fecha_compra',
                'eventos.titulo as evento',
                'eventos.fecha',
                'eventos.hora_inicio',
                'eventos.imagen'
            )
            ->orderBy('tickets.id', 'desc')
            ->get();

        // Obtener los asientos
        foreach ($tickets as $t) {
            $t->asientos = DB::table('reservas')
                ->join('asientos', 'asientos.id', '=', 'reservas.asiento_id')
                ->join('ubicaciones', 'ubicaciones.id', '=', 'asientos.ubicacion_id')
                ->where('reservas.ticket_id', $t->ticket_id)
                ->select(
                    'asientos.fila',
                    'asientos.numero',
                    'ubicaciones.nombre as ubicacion',
                    'ubicaciones.precio'
                )
                ->get();
        }

        return response()->json([
            "success" => true,
            "tickets" => $tickets
        ]);
    }

    // Obtener los tickets de un cliente (robusto, evita fallos)
    public function misTicketsCliente($cliente_id)
    {
        // Validación básica del parámetro
        if (!is_numeric($cliente_id)) {
            return response()->json([
                "success" => false,
                "message" => "Parámetro inválido: cliente_id debe ser numérico."
            ], 422);
        }

        try {
            // Traer tickets con datos del evento (leftJoin para no perder el ticket si el evento falta)
            $tickets = DB::table('tickets')
                ->leftJoin('eventos', 'eventos.id', '=', 'tickets.evento_id')
                ->where('tickets.cliente_id', (int)$cliente_id)
                ->select(
                    'tickets.id',
                    'tickets.cliente_id',
                    'tickets.evento_id',
                    'tickets.fecha_compra',
                    'tickets.estado',
                    'tickets.precio',
                    'tickets.usado',
                    'eventos.titulo',
                    'eventos.fecha as fecha_evento',
                    'eventos.hora_inicio',
                    'eventos.hora_final',
                    'eventos.imagen as imagen_evento'
                )
                ->orderBy('tickets.id', 'desc')
                ->get();

            // Si no hay tickets, responder vacío de forma exitosa
            if ($tickets->isEmpty()) {
                return response()->json([
                    "success" => true,
                    "tickets" => []
                ]);
            }

            // Obtener todos los asientos en una sola consulta y agrupar por ticket
            $ticketIds = $tickets->pluck('id')->all();

            $asientosAgrupados = DB::table('reserva_asientos')
                ->join('asientos_eventos', 'asientos_eventos.id', '=', 'reserva_asientos.asiento_evento_id')
                ->join('asientos', 'asientos.id', '=', 'asientos_eventos.asiento_id')
                ->join('ubicacion_asientos', 'ubicacion_asientos.id', '=', 'asientos.ubicacion_id')
                ->whereIn('reserva_asientos.ticket_id', $ticketIds)
                ->select(
                    'reserva_asientos.ticket_id',
                    'ubicacion_asientos.ubicacion',
                    'asientos.fila',
                    'asientos.numero'
                )
                ->get()
                ->groupBy('ticket_id');

            // Enriquecer cada ticket con sus asientos y QR (si existe)
            foreach ($tickets as $t) {
                $t->asientos = ($asientosAgrupados->get($t->id) ?? collect())->values();

                $qrPath = public_path("qr/ticket_{$t->id}.svg");
                $t->qr = file_exists($qrPath) ? asset("qr/ticket_{$t->id}.svg") : null;
            }

            return response()->json([
                "success" => true,
                "tickets" => $tickets
            ]);
        } catch (\Throwable $e) {
            // Respuesta genérica para evitar exponer detalles internos
            return response()->json([
                "success" => false,
                "message" => "Error al obtener los tickets del cliente."
            ], 500);
        }
    }

    // Descargar ticket en PDF
    public function descargarPdfTicket($id)
    {
        // Obtener información del ticket
        $ticket = DB::table('tickets')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->join('clientes', 'clientes.id', '=', 'tickets.cliente_id')
            ->where('tickets.id', $id)
            ->select(
                'tickets.id AS ticket_id',
                'tickets.precio',
                'tickets.fecha_compra',
                'tickets.estado',
                'eventos.titulo',
                'eventos.fecha as fecha_evento',
                'eventos.hora_inicio',
                'eventos.hora_final',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.documento',
                'clientes.correo'
            )
            ->first();

        if (!$ticket) {
            return response()->json([
                "success" => false,
                "message" => "Ticket no encontrado"
            ], 404);
        }

        // Obtener asientos
        $asientos = DB::table('reserva_asientos')
            ->join('asientos_eventos', 'asientos_eventos.id', '=', 'reserva_asientos.asiento_evento_id')
            ->join('asientos', 'asientos.id', '=', 'asientos_eventos.asiento_id')
            ->join('ubicacion_asientos', 'ubicacion_asientos.id', '=', 'asientos.ubicacion_id')
            ->join('precios_eventos', 'precios_eventos.id', '=', 'asientos_eventos.precio_id') // <-- FALTABA ESTO
            ->where('reserva_asientos.ticket_id', $id)
            ->select(
                'ubicacion_asientos.ubicacion',
                'asientos.fila',
                'asientos.numero',
                'precios_eventos.precio'
            )
            ->get();


        // 🔥 Convertir a arrays para que blade pueda usar $a['fila']
        $asientos = $asientos->map(function ($a) {
            return [
                "fila"      => $a->fila,
                "numero"    => $a->numero,
                "ubicacion" => $a->ubicacion,
                "precio_asiento" => $a->precio,
                "precio"    => 0 // si no usas precio aquí
            ];
        });

        // Verificar si existe el QR
        $qrPath = public_path("qr/ticket_{$ticket->ticket_id}.svg");

        if (!file_exists($qrPath)) {
            return response()->json([
                "success" => false,
                "message" => "El QR del ticket no existe en el servidor."
            ], 500);
        }

        // Cargar el contenido del SVG y codificarlo en base64
        $qrBase64 = base64_encode(file_get_contents($qrPath));

        // Generar PDF
        $pdf = SnappyPdf::loadView('pdf.descargar_ticket', [
            'ticket'   => $ticket,
            'asientos' => $asientos,
            'qr'       => $qrBase64
        ]);

        $pdf->setPaper('a4')->setOption('margin-top', '10mm');

        return $pdf->download("Ticket-{$id}.pdf");
    }
}
