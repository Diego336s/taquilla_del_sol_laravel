<?php

namespace App\Http\Controllers;

use App\Models\Pagos;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator as IlluminateValidationValidator;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PagosController extends Controller
{
    public function index(){
        $pagos = Pagos::all();
        return response()->json($pagos, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            "ticket_id" => "required|integer|exists:ticket,id",
            "metodo_pago" => "required|in:tarjeta,paypal,efectivo,transferencia",
            "monto" => "required|decimal",
            "fecha_pago" => "required|date",
            "estado" => "required|in:pendiente,aprobado,rechazado",
        ]);

        if ($validator->fails()) {
            return response()->json($validator-> errors(), 422);
        }

        $validator_datos = $validator->validate();

        $pagos = Pagos::create($validator_datos);

        return response()->json([
            'success' =>true,
            'message' => "Pago generado correctamente",
            'data' => $pagos,
        ], 201);
    }

    public function show(string $id){
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }
        return response()->json($pagos);
    }

    public function update(Request $request, string $id){
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }

        $validator = Validator::make($request->all(),[
            "ticket_id" => "integer|exists:ticket,id",
            "metodo_pago" => "in:tarjeta,paypal,efectivo,transferencia",
            "monto" => "decimal",
            "fecha_pago" => "date",
            "estado" => "in:pendiente,aprobado,rechazado",
        ]);

        if ($validator->fails()) {
            return response()->json($validator-> errors(), 422);
        }

        $validator_datos = $validator->validate();

        $pagos->update($validator_datos);

        return response()->json([
            'success' =>true,
            'message' => "Pago actualizado correctamente",
            'data' => $pagos,
        ], 201);
    }

    public function destroy(string $id){
        $pagos = Pagos::find($id);
        if (!$pagos) {
            return response()->json(['message' => "Pago no encontrado", 404]);
        }

        $pagos->delete();

        return response()->json([
            'success' =>true,
            'message' => "Pago eliminado correctamente",
            'data' => $pagos,
        ], 201);

    }

      public function crearSesionPago(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Asientos seleccionados desde el front
            $asientos = $request->input('asientos');
            $total = $request->input('total');

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'cop', // o 'usd'
                        'product_data' => [
                            'name' => 'Reserva de Asientos',
                            'description' => 'Pago de asientos seleccionados',
                        ],
                        'unit_amount' => $total * 100, // Stripe usa centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . '/pago-exitoso',
                'cancel_url' => env('FRONTEND_URL') . '/pago-cancelado',
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
}
