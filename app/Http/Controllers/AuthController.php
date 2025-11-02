<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\clientes;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function sendResetCode(Request $request)
    {
        // Validar el correo
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email|exists:clientes,correo'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El correo no está registrado o es inválido.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar cliente
        $cliente = clientes::where('correo', $request->correo)->first();

        // Generar un código de 6 dígitos
        $codigo = random_int(100000, 999999);

        // Guardar temporalmente el código (ejemplo: en la base de datos)
        $cliente->codigo_recuperacion = $codigo;
        $cliente->save();

        // Enviar correo
        try {
            Mail::send('emails.codigo_recuperacion', ['codigo' => $codigo, 'nombre' => $cliente->nombre], function ($message) use ($cliente) {
                $message->to($cliente->correo)
                    ->subject('Código para restablecer tu contraseña');
            });

            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un correo con tu código de recuperación.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar el correo. Intenta más tarde.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restablecerClave(Request $request)
    {
        $request->validate([
            'codigo_recuperacion' => 'required|numeric',
            'nueva_clave' => 'required|min:6',
            'confirmar_nueva_clave' => 'required|same:nueva_clave',
        ]);

        // Buscar usuario por código
        $cliente = clientes::where('codigo_recuperacion', $request->codigo_recuperacion)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Código inválido o expirado.']);
        }

        // Actualizar contraseña y limpiar código
        $cliente->clave = Hash::make($request->nueva_clave);
        $cliente->codigo_recuperacion = null;
        $cliente->save();

        return response()->json(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
    }
}
