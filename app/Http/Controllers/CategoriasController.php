<?php

namespace App\Http\Controllers;

use App\Models\categorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = categorias::all();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $categoria = categorias::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $categoria
        ], 201);
    }

    public function show(string $id)
    {
        $categoria = categorias::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    public function update(Request $request, string $id)
    {
        $categoria = categorias::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 400);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $categoria->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    public function destroy(string $id)
    {
        $categoria = categorias::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 400);
        }

        $categoria->delete();

        return response()->json(['message' => 'Categoría eliminada']);
    }
}
