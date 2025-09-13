<?php

namespace App\Http\Controllers;

use App\Models\asientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsientosController extends Controller
{
    public function index()
    {
        $asientos = asientos::all();
        return response()->json($asientos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "fila" => "required|string",
            "numero" => "required|integer",
            "disponible" => "required|boolean"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ], 400);
        }
        $asiento = asientos::create($validator->validated());
        return response()->json([
        "success" => true,
        "message"=> "Asiento creado correctamente" 
        ]);
    }
}
