<?php

namespace App\Http\Controllers;

use App\Mail\TicketUsuario;
use App\Models\asientosEventos;
use App\Models\Pagos;
use App\Models\reservaAsientos;
use App\Models\Ticket;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Expr\Array_;
use Stripe\Checkout\Session;
use Stripe\Stripe;

use function Symfony\Component\Clock\now;

class PagosController extends Controller
{
    public function index()
    {
        $pagos = Pagos::all();
        return response()->json($pagos, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "ticket_id" => "required|integer|exists:ticket,id",
            "metodo_pago" => "required|in:tarjeta,paypal,efectivo,transferencia",
            "monto" => "required|decimal",
            "fecha_pago" => "required|date",
            "estado" => "required|in:pendiente,aprobado,rechazado",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validate();

        $pagos = Pagos::create($validator_datos);

        return response()->json([
            'success' => true,
            'message' => "Pago generado correctamente",
            'data' => $pagos,
        ], 201);
    }

    public function show(string $id)
    {
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }
        return response()->json($pagos);
    }

    public function update(Request $request, string $id)
    {
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }

        $validator = Validator::make($request->all(), [
            "ticket_id" => "integer|exists:ticket,id",
            "metodo_pago" => "in:tarjeta,paypal,efectivo,transferencia",
            "monto" => "decimal",
            "fecha_pago" => "date",
            "estado" => "in:pendiente,aprobado,rechazado",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validate();

        $pagos->update($validator_datos);

        return response()->json([
            'success' => true,
            'message' => "Pago actualizado correctamente",
            'data' => $pagos,
        ], 201);
    }

    public function destroy(string $id)
    {
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }

        $pagos->delete();

        return response()->json([
            'success' => true,
            'message' => "Pago eliminado correctamente",
            'data' => $pagos,
        ], 201);
    }

    public function crearSesionPago(Request $request)
    {
        // 1. VALIDACIÓN
        $validator = Validator::make($request->all(), [
            "asientos"   => "required|array|min:1|max:10",
            "id_evento"  => "required|integer|exists:eventos,id",
            "total"      => "required|integer|min:1",
            "id_cliente"  => "required|integer|exists:clientes,id",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Error de validación.",
                "errors"  => $validator->errors()
            ], 422);
        }

        // Convertir array de asientos a string CSV
        // [1,2,3] → "1,2,3"
        $asientosReservados = $request->asientos;

        // 2. VALIDAR QUE TODOS LOS ASIENTOS ESTÉN DISPONIBLES
        foreach ($asientosReservados as $id) {

            $asiento = DB::table('asientos_eventos as ae')
                ->join('asientos as a', 'ae.asiento_id', '=', 'a.id')
                ->join('ubicacion_asientos as u', 'a.ubicacion_id', '=', 'u.id')
                ->join('precios_eventos as p', 'ae.precio_id', '=', 'p.id')
                ->where('ae.id', $id)
                ->select(
                    'a.fila',
                    'a.numero',
                    'u.ubicacion',
                    'p.precio',
                    'ae.disponible'
                )
                ->first();

            if (!$asiento) {
                return response()->json([
                    "success" => false,
                    "message" => "Uno de los asientos no existe."
                ]);
            }

            if ($asiento->disponible == false) {
                return response()->json([
                    "success" => false,
                    "message" => "El asiento $asiento->numero de la fila $asiento->fila (ubicación $asiento->ubicacion) NO está disponible."
                ]);
            }
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));


            $id_usario = $request->id_cliente;
            $id_evento = $request->id_evento;
            $total = $request->input('total');
            $asientosIncriptados = rtrim(strtr(base64_encode(gzcompress(json_encode($asientosReservados))), '+/', '-_'), '=');
            $id_usarioIncriptado = rtrim(strtr(base64_encode(gzcompress(json_encode($id_usario))), '+/', '-_'), '=');
            $id_eventoIncriptado = rtrim(strtr(base64_encode(gzcompress(json_encode($id_evento))), '+/', '-_'), '=');
            $totalIncriptados= rtrim(strtr(base64_encode(gzcompress(json_encode($total))), '+/', '-_'), '=');

            // Crear sesión de Stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'cop',
                        'product_data' => [
                            'name' => 'Reserva de Asientos',
                            'description' => 'Pago de asientos seleccionados',
                        ],
                        'unit_amount' => $total * 100,  // centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',

                // Enviar los IDs de asientos como query params
                'success_url' => env('FRONTEND_URL') . "/api/pago-exitoso/$asientosIncriptados/$id_usarioIncriptado/$totalIncriptados/$id_eventoIncriptado",
                'cancel_url'  => env('FRONTEND_URL') . '/pago-cancelado',
            ]);

            return response()->json([
                'url' => $session->url
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function pagoExitoso($asientosIncriptados, $idClientesEncriptado, $totalEncriptado, $idEventoEncriptado)
    {
        DB::beginTransaction();

        $asientos = json_decode(gzuncompress(base64_decode(strtr($asientosIncriptados, '-_', '+/'))));
        $idClientes = json_decode(gzuncompress(base64_decode(strtr($idClientesEncriptado, '-_', '+/'))));
        $total = json_decode(gzuncompress(base64_decode(strtr($totalEncriptado, '-_', '+/'))));
        $idEvento = json_decode(gzuncompress(base64_decode(strtr($idEventoEncriptado, '-_', '+/'))));
        if (!is_array($asientos) || empty($asientos) || !$asientos) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "No llegaron asientos válidos"
            ], 400);
        }


        try {

            $ticket = Ticket::create([
                'evento_id' => $idEvento,
                'cliente_id' => $idClientes,
                'precio' => $total,
                'estado' => "comprado",
                'fecha_compra' => now(),
            ]);

            if (!$ticket) {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    "message" => "No sea podido crear el ticket"
                ]);
            }

            foreach ($asientos as $asiento) {
                $id = intval($asiento);
                asientosEventos::where("id", $id)->update([
                    "disponible" => false
                ]);

                reservaAsientos::create([
                    "ticket_id" => $ticket->id,
                    "asiento_evento_id" => $id
                ]);
            }

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
                ->where('tickets.id', $ticket->id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    "success" => false,
                    "message" => "El ticket no existe"
                ], 404);
            }

            // --- AGRUPAR ---
            $infTicket = $data->first();

            $asientos = $data->map(function ($item) {
                return [
                    "fila" => $item->fila,
                    "numero" => $item->numero,
                    "ubicacion" => $item->ubicacion,
                    "precio" => $item->precio_asiento,
                ];
            });



            $correoRemitente = $infTicket->correo;
            Mail::to($correoRemitente)->send(new TicketUsuario($infTicket, $asientos));

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Reservacion de asientos exitosa"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error " . $e->getMessage()
            ], 400);
        }
    }

}
