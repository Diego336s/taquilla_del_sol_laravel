<?php

namespace App\Http\Controllers;

use App\Models\Administradores;
use App\Models\clientes;
use App\Models\Empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministradoresController extends Controller
{
    public function index()
    {
        $administradores = Administradores::all();
        return response()->json($administradores);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'documento' => 'required|integer|unique:administradores,documento',
            'telefono' => 'required|integer|min:10',
            'correo' => 'required|email|unique:administradores,correo',
            'clave' => 'required|string|min:6',
            'fecha_nacimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
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

        $administradores = Administradores::create([
            "nombres" => $request->nombres,
            "apellidos" => $request->apellidos,
            "documento" => $request->documento,
            "telefono" => $request->telefono,
            "telefono" => $request->telefono,
            "correo" => $request->correo,
            "clave" => Hash::make($request->clave),
            "fecha_nacimiento" => $request->fecha_nacimiento,

        ]);


        $token = $administradores->createToken("auth_token", ["Admin"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Administrador $request->nombres registrado correctamente",
            "user" => $administradores,
            "token_access" => $token,
            "token_type" => "Bearer"
        ], 200);
    }

    public function show(string $id)
    {
        $administradores = Administradores::find($id);
        if (!$administradores) {
            return response()->json(['message' => 'Administrador no encontrado'], 404);
        }
        return response()->json($administradores);
    }

    public function update(Request $request, string $id)
    {
        $administradores = Administradores::find($id);
        if (!$administradores) {
            return response()->json(['message' => 'Administrador no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'documento' => 'sometimes|required|integer',
            'telefono' => 'sometimes|required|integer|min:10',
            'correo' => 'sometimes|required|string|max:255',
            'fecha_nacimiento' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $administradores->update($validator->validate());

        return response()->json($administradores);
    }

    public function cambiarClave(Request $request, string $id)
    {
        $administradores = Administradores::find($id);
        if (!$administradores) {
            return response()->json(['message' => 'Administrador no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'clave' => 'required|string|min:6',
        ]);

        $administradores->clave = Hash::make($validatedData['clave']);
        $administradores->save();
        return response()->json($administradores);
    }

    public function destroy(string $id)
    {
        $administradores = Administradores::find($id);
        if (!$administradores) {
            return response()->json(['message' => 'Administrador no encontrado']);
        }

        $administradores->delete();
        return response()->json(['message' => 'Administrador eliminado correctamente']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "documento" => "required|integer",
            "clave" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ]);
        }

        $admin = Administradores::where("documento", $request->documento)->first();

        if (!$admin) {
            return response()->json([
                "success" => false,
                "message" => "No encontramos tu cuenta",
            ]);
        }

        if (!$admin || !Hash::check($request->clave, $admin->clave)) {
            return response()->json([
                "success" => false,
                "error" => "Credenciales Incorrectas",
            ]);
        }

        $token = $admin->createToken("auth_token", ["Admin"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Inicio de sesion exitoso",
            "token" => $token,
            "token_type" => "Bearer",
            "admin" => $admin
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

    //Restablecer clave administrador
    public function olvideMiClaveAdmin(Request $request)
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

        // Buscar admin por correo
        $admin = Administradores::where("correo", $request->correo)->first();

        if (!$admin) {
            return response()->json([
                "success" => false,
                "message" => "No se encontró un cliente con ese correo"
            ], 404);
        }

        // Actualizar clave
        $admin->update([
            "clave" => Hash::make($request->clave)
        ]);

        return response()->json([
            "success" => true,
            "message" => "Cambio de clave exitoso"
        ], 200);
    }
}
