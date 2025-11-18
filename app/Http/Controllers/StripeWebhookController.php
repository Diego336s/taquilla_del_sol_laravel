<?php

namespace App\Http\Controllers;

use App\Models\asientosEventos;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // lo obtienes de Stripe Dashboard

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 🔹 Detectar el tipo de evento
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // ✅ Aquí actualizas tu base de datos (ejemplo)
                Log::info('✅ Pago completado', ['session' => $session->id]);
                $asientos = explode(',', $session->metadata->asientos);
                $id_evento = $session->metadata->id_evento;
                $total = $session->metadata->total;

                $asientosReservados = array_filter(array_map('trim', $asientos));

                foreach ($asientosReservados as $asiento) {
                    $id = intval($asiento);
                    if ($id > 0) {
                        asientosEventos::where("id", $id)->update([
                            "disponible" => false
                        ]);
                    }
                }

                break;

            case 'payment_intent.payment_failed':
                Log::warning('⚠️ Pago fallido', ['event' => $event]);
                break;

            default:
                Log::info('Evento no manejado: ' . $event->type);
        }

        return response()->json(['status' => 'ok']);
    }
}
