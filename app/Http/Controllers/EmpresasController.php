<?php

namespace App\Http\Controllers;

use App\Models\Administradores;
use App\Models\clientes;
use App\Models\Empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpresasController extends Controller
{
    /**
     * Listar todas las empresas
     */
    public function index()
    {
        $empresas = Empresas::all();
        return response()->json($empresas, 200);
    }
//crear una nueva empresa 
    public function store(Request $request)
    {
        $validar_datos = Validator::make($request->all(),[
            'nombre_empresa'          => 'required|string|max:200',
            'nit'                     => 'required|integer|unique:empresas,nit',
            'representante_legal'     => 'required|string|max:200',
            'documento_representante' => 'required|integer|unique:empresas,documento_representante',
            'nombre_contacto'         => 'nullable|string|max:200',
            'telefono'                => 'nullable|string|max:20',
            'correo'                  => 'required|email|unique:empresas,correo',
            'clave'                   => 'required|string|max:200',
        ]);

        if ($validar_datos->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validar_datos->errors()
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

        $empresa = Empresas::create([
            "nombre_empresa" => $request->nombre_empresa,
            "nit" => $request->nit,
            "representante_legal" => $request->representante_legal,
            "documento_representante" => $request->documento_representante,
            "nombre_contacto" => $request->nombre_contacto,
            "telefono" => $request->telefono,
            "correo" => $request->correo,            
            "clave" => Hash::make($request->clave)
        ]);

        $token = $empresa->createToken("auth_token", ["Empresa"])->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Empresa $request->nombre registrada correctamente",
            "user" => $empresa,
            "token_access" => $token,
            "token_type" => "Bearer"
        ],200);
    }

    // Mostrar una empresa por ID

    public function show(string $id)
    {
        $empresa = Empresas::findOrFail($id);
        return response()->json($empresa, 200);
    }

    public function update(Request $request, string $id)
    {
        $empresa = Empresas::findOrFail($id);

        $validated = $request->validate([
            'nombre_empresa'          => 'sometimes|string|max:200',
            'nit'                     => 'sometimes|string|max:50|unique:empresas,nit,' . $empresa->id,
            'representante_legal'     => 'sometimes|string|max:200',
            'documento_representante' => 'sometimes|string|max:15',
            'nombre_contacto'         => 'nullable|string|max:200',
            'telefono'                => 'nullable|string|max:20',
            'correo'                  => 'sometimes|email|max:200|unique:empresas,correo,' . $empresa->id,
            'clave'                   => 'sometimes|string|max:200',
        ]);

        // Encriptar la clave si viene en la actualización
        if (isset($validated['clave'])) {
            $validated['clave'] = Hash::make($validated['clave']);
        }

        $empresa->update($validated);

        return response()->json($empresa, 200);
    }

    public function cambioClave(Request $request, string $id)
    {
        $empresa = Empresas::findOrFail($id);
        $validated = $request->validate([
            'clave' => 'required|string|max:6',
        ]);

        // Encriptar la clave
        $validated['clave'] = Hash::make($validated['clave']);
        $empresa->update($validated);
        return response()->json(['message' => 'Clave actualizada correctamente'], 200);
    }


    public function destroy(string $id)
    {
        $empresa = Empresas::findOrFail($id);
        $empresa->delete();

        return response()->json(['message' => 'Empresa eliminada correctamente'], 200);
    }


     public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nit" => "required",
            "clave" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 402);
        }

        $Empresas = Empresas::where("nit", $request->nit)->first();

        if (!$Empresas || !Hash::check($request->clave, $Empresas->clave)) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas",
            ], 401);
        }

        $token = $Empresas->createToken("auth_token", ["Empresa"])->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Inicio de sesion exitoso",
            "token" => $token,
            "token_type" => "Bearer"
        ]);
    }
}
