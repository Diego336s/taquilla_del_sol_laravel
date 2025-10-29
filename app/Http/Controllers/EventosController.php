<?php

namespace App\Http\Controllers;

use App\Models\Eventos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EventosController extends Controller
{
    public function index()
    {
        $eventos = Eventos::all();
        return response()->json($eventos, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'        => 'required|string|max:200',
            'descripcion'   => 'nullable|string',
            'fecha'  => 'required|date',
            'hora_inicio'     => 'required|time',
            'hora_fin'     => 'required|time',
            'estado'        => 'required|in:activo,inactivo',
            'empresa_id'    => 'required|integer|exists:empresa,id',
            'categoria_id'  => 'required|integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validate();

        $eventos = Eventos::create($validator_datos);

        return response()->json([
            'success' => true,
            'message' => "Evento creado correctamente",
            'data' => $eventos
        ], 201);
    }

    public function show(string $id)
    {
        $eventos = Eventos::find($id);
        if (!$eventos) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }
        return response()->json($eventos);
    }

    public function update(Request $request, string $id)
    {
        $eventos = Eventos::find($id);

        if (!$eventos) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo'        => 'string|max:200',
            'descripcion'   => 'nullablestring',
            'fecha'  => 'required|date',
            'hora_inicio'     => 'required|time',
            'hora_fin'     => 'required|time',
            'estado'        => 'in:activo,inactivo',
            'empresa_id'    => 'integer|exists:empresa,id',
            'categoria_id'  => 'integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $eventos->update($validator->validate());

        return response()->json($eventos);
    }

    public function destroy(string $id)
    {
        $eventos = Eventos::find($id);
        if (!$eventos) {
            return response()->json(['message' => 'evento no encontrado'], 404);
        }
        $eventos->delete();
        return response()->json(['message' => 'Evento eliminado correctamente']);
    }
}
