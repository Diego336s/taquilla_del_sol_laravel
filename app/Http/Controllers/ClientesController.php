<?php

namespace App\Http\Controllers;

use App\Models\Administradores;
use App\Models\clientes;
use App\Models\Empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            "success" => true,
            "user" => $request->user()
        ]);
    }

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
            "sexo" => "required|in:F,M",
            "correo" => "required|email|unique:clientes,correo",
            "clave" => "required|string|min:6"
        ]);


        if ($validator_datos->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator_datos->errors()
            ], 400);
        }

        $correoExistenteCliente = clientes::where("correo", $request->correo)->exists();
        $correoExistenteEmpresa = Empresas::where("correo", $request->correo)->exists();
        $correoExistenteAdministradores = Administradores::where("correo", $request->correo)->exists();
        if ($correoExistenteAdministradores || $correoExistenteCliente || $correoExistenteEmpresa) {
            return response()->json([
                "success" => false,
                "message" => "Correo $request->correo ya se encuetra registrado."
            ]);
        }

        $cliente = clientes::create([
            "nombre" => $request->nombre,
            "apellido" => $request->apellido,
            "documento" => $request->documento,
            "fecha_nacimiento" => $request->fecha_nacimiento,
            "telefono" => $request->telefono,
            "sexo" => $request->sexo,
            "correo" => $request->correo,            
            "clave" => Hash::make($request->clave)
        ]);
        $token = $cliente->createToken("auth_token", ["Cliente"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Cliente $request->nombre registrado correctamente",
            "user" => $cliente,
            "token_access" => $token,
            "token_type" => "Bearer"
        ]);
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
            "sexo" => "required|in:F,M",
            "telefono" => "integer"
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "correo" => "required|email",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 402);
        }

        $cliente = clientes::where("correo", $request->correo)->first();

        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "No encontramos tu cuenta",
            ], 401);
        }

        if (!$cliente || !Hash::check($request->clave, $cliente->clave)) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas",
            ], 401);
        }

        $token = $cliente->createToken("auth_token", ["Cliente"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Inicio de sesion exitoso",
            "token" => $token,
            "token_type" => "Bearer"
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay usuario autenticado o token inválido'
        ], 401);
    }
}
