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

    // 🔥 AHORA DEVUELVE EL FORMATO CORRECTO PARA EL FRONTEND
    public function index()
    {
        $clientes = clientes::all();

        return response()->json([
            "success" => true,
            "clientes" => $clientes
        ]);
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
                "message" => $validator_datos->errors()
            ], 400);
        }

        // Validación de correo único en todas las tablas
        $correoExistenteCliente = clientes::where("correo", $request->correo)->exists();
        $correoExistenteEmpresa = Empresas::where("correo", $request->correo)->exists();
        $correoExistenteAdministradores = Administradores::where("correo", $request->correo)->exists();

        if ($correoExistenteAdministradores || $correoExistenteCliente || $correoExistenteEmpresa) {
            return response()->json([
                "success" => false,
                "message" => "Correo $request->correo ya se encuentra registrado."
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
            "rol" => "Cliente",
            "token_type" => "Bearer"
        ], 200);
    }

    public function show($id)
    {
        $cliente = Clientes::find($id);

        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado",
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $cliente
        ], 200);
    }

    public function updateCliente(Request $request, string $id)
    {
        $cliente = clientes::find($id);

        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombre" => "sometimes|string|max:100",
            "apellido" => "sometimes|string|max:100",
            "documento" => "sometimes|integer|unique:clientes,documento," . $cliente->id,
            "fecha_nacimiento" => "sometimes|date",
            "sexo" => "sometimes|in:F,M",
            "telefono" => "sometimes|string|max:15"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Errores de validación",
                "errors" => $validator->errors()
            ], 400);
        }

        $cliente->update($validator->validated());

        return response()->json([
            "success" => true,
            "message" => "Perfil actualizado correctamente",
            "cliente" => $cliente
        ], 200);
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
                "message" => "Error de validaciones",
                "error" => $validator_clave->errors()
            ], 400);
        }

        if (Hash::check($request->clave, $cliente->clave)) {
            return response()->json([
                "success" => false,
                "message" => "La contraseña debe ser diferente a la actual.",
            ]);
        }

        $cliente->update([
            "clave" => Hash::make($request->clave)
        ]);

        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            "success" => true,
            "message" => "Clave cambiada exitosamente. Inicia sesión nuevamente."
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "correo" => "required|email",
            "clave" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 402);
        }

        $cliente = clientes::where("correo", $request->correo)->first();

        if (!$cliente || !Hash::check($request->clave, $cliente->clave)) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas",
            ]);
        }

        $token = $cliente->createToken("auth_token", ["Cliente"])->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Inicio de sesión exitoso",
            "token" => $token,
            "token_type" => "Bearer",
            "cliente" => $cliente
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
        ]);
    }

    public function olvideMiClave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "correo" => "required|string|email",
            "clave"  => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $cliente = clientes::where("correo", $request->correo)->first();

        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "No se encontró un cliente con ese correo"
            ], 404);
        }

        $cliente->update([
            "clave" => Hash::make($request->clave)
        ]);

        return response()->json([
            "success" => true,
            "message" => "Cambio de clave exitoso"
        ], 200);
    }

    public function cambiarCorreo(Request $request, string $id)
    {
        $cliente = clientes::find($id);

        if (!$cliente) {
            return response()->json(["message" => "Cliente no encontrado"]);
        }

        $validator = Validator::make($request->all(), [
            "correo" => "string|email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        $correoExistenteCliente = clientes::where("correo", $request->correo)->exists();
        $correoExistenteAdmin = Administradores::where("correo", $request->correo)->exists();
        $correoExistenteEmpresa = Empresas::where("correo", $request->correo)->exists();

        if ($correoExistenteAdmin || $correoExistenteEmpresa || $correoExistenteCliente) {
            return response()->json([
                "success" => false,
                "message" => "El correo $request->correo ya se encuentra registrado"
            ]);
        }

        $cliente->update($validator->validated());

        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            "success" => true,
            "message" => "Correo actualizado, inicia sesión nuevamente."
        ], 200);
    }
}
