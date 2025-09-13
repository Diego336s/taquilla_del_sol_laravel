<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index(){
        $ticket = Ticket::all();
        return response()->json($ticket, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'evento_id'    => 'required|integer|exists:eventos,id',
            'cliente_id'   => 'required|integer|exists:clientes,id',
            'tipo'         => 'required|in:general,vip,estudiante',
            'precio'       => 'required|numeric|min:0',
            'estado'       => 'required|in:reservado,comprado,cancelado',
            'fecha_compra' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $validator_datos = $validator->validate();
        $ticket = Ticket::create($validator_datos);

        return response()->json([
            'success' =>true,
            'message' => "Ticket generado correctamente",
            'data' => $ticket,
        ], 201);
    }

    public function show(string $id){
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }
        return response()->json($ticket);
    }

    public function update(Request $request, string $id) {
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
            return response()->json($validator->errors(),422);
        }

        $ticket->update($validator->validate());

        return response()->json(['success' => true,
        'message' => 'Ticket actualizado correctamente',
        'data' => $ticket],200);
    }

    public function destroy(string $id){
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'],404);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket eliminado correctamente'], 200);
    }
}

