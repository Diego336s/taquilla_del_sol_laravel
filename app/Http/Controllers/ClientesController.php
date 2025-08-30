<?php

namespace App\Http\Controllers;

use App\Models\clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index()
    {
        $clientes = clientes::all();
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $validator_datos = Validator::make($request->all(), [
            "nombre" => "required|string",
            "apellido" => "required|string",
            "documento" => "required|integer|unique:clientes,documento",
            "fecha_nacimiento" => "required|date",
            "telefono" => "required|integer",
            "correo" => "required|email|unique:clientes,correo",
        ]);

        $validator_clave = Validator::make($request->all(), [
            "clave" => "required|string|min:6"
        ]);

        if ($validator_datos->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator_datos->errors()
            ], 400);
        } else if ($validator_clave->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator_clave->errors()
            ], 400);
        };
        $datos = $validator_datos->validated();
        $datos["clave"] = $validator_clave->validated();



        $cliente = clientes::create($datos);
        return response()->json($cliente, 201);
    }

    public function show(string $id)
    {
        $cliente = clientes::find($id);
        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 404);
        }
        return response()->json($cliente, 200);
    }

    public function update(Request $request, string $id)
    {

        $cliente = clientes::find($id);
        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 404);
        }


        $validator = Validator::make($request->all(), [
            "nombre" => "string",
            "apellido" => "string",
            "documento" => "integer|unique:clientes,documento",
            "fecha_nacimiento" => "date",
            "telefono" => "integer",
            "correo" => "email|unique:clientes,correo"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ], 400);
        };

        $cliente = clientes::update($validator->validated());
        return response()->json($cliente, 200);
    }

    public function destroy(string $id)
    {
        $cliente = clientes::find($id);
        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 404);
        }
        $cliente->delete();
        return response()->json([
            "success" => true,
            "message" => "Cliente eliminado correctamente"
        ], 200);
    }

    public function cambiarClave(Request $request, string $id)
    {
        $cliente = clientes::find($id);
        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 404);
        }

        $validator_clave = Validator::make($request->all(), [
            "clave" => "required|string|min:6"
        ]);

        if ($validator_clave->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator_clave->errors()
            ], 400);
        };

        $cliente = clientes::update($validator_clave->validated());
        return response()->json($cliente, 200);
    }
}
