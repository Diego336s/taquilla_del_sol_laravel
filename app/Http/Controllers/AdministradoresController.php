<?php

namespace App\Http\Controllers;

use App\Models\Administradores;
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
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento' => 'required|integer',
            'telefono' => 'required|integer|min:10',
            'correo' => 'required|string|max:255',
            'clave' => 'required|string|min:6',
            'fecha_nacimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $validatedData['clave'] = Hash::make($validatedData['clave']);

        $administradores = Administradores::create($validatedData);
         $token = $administradores->createToken("auth_token", ["Admin"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Administrador $request->nombre registrado correctamente",
            "user" => $administradores,
            "token_access" => $token,
            "token_type" => "Bearer"
        ]); 

       
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

        $validator = Validator::make($request->all(),[
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

    public function cambiarClave(Request $request, string $id){
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

    public function destroy(string $id){
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
            "correo" => "required|email",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ], 402);
        }

        $admin = Administradores::where("correo", $request->correo)->first();

        if (!$admin || !Hash::check($request->clave, $admin->clave)) {
            return response()->json([
                "success" => false,
                "error" => "Credenciales incorrectas",
            ], 401);
        }

        $token = $admin->createToken("auth_token", ["Admin"])->plainTextToken;
        return response()->json([
            "success" => true,
            "token" => $token,
            "token_type" => "Bearer"
        ]);
    }

}


