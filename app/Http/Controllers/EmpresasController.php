<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller
{
    /**
     * Listar todas las empresas
     */
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas, 200);
    }
//crear una nueva empresa 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_empresa'          => 'required|string|max:200',
            'nit'                     => 'required|string|max:50',
            'representante_legal'     => 'required|string|max:200',
            'documento_representante' => 'required|integer|max:10',
            'nombre_contacto'         => 'nullable|string|max:200',
            'telefono'                => 'nullable|string|max:20',
            'correo'                  => 'required|email|max:200',
            'clave'                   => 'required|string|max:200',
        ]);

        $validated['clave'] = bcrypt($validated['clave']);
        $empresa = Empresa::create($validated);

        return response()->json($empresa, 201);
    }

    // Mostrar una empresa por ID

    public function show(string $id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json($empresa, 200);
    }

    public function update(Request $request, string $id)
    {
        $empresa = Empresa::findOrFail($id);

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

    public function cambioCable(Request $request, string $id)
    {
        $empresa = Empresa::findOrFail($id);
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
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return response()->json(['message' => 'Empresa eliminada correctamente'], 200);
    }
}
