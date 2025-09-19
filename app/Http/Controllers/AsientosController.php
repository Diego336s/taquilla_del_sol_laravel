<?php

namespace App\Http\Controllers;

use App\Models\Asientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsientosController extends Controller
{
    public function index()
    {
        $Asientos = Asientos::all();
        return response()->json($Asientos);
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
        $asiento = Asientos::create($validator->validated());
        return response()->json([
        "success" => true,
        "message"=> "Asiento creado correctamente" 
        ]);
    }
    public function show(string $id)
    {
        $asiento = Asientos::find($id);
        if (!$asiento) {
            return response()->json([
                "success" => false,
                "message" => "Asiento no encontrado"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "data" => $asiento
        ]);
    }
    public function update(Request $request, string $id){
    $asiento = Asientos::find($id);
    if(!$asiento){
        return response()->json([
            "success" => false,
            "message" => "Asiento no encontrado"
        ],404);
    }

    $validator = Validator::make($request->all(), [
        "fila" => "sometimes|required|string",
        "numero"=> "sometimes|required|integer",
        "disponible" => "sometimes|required|boolean"
    ]);

    if($validator->fails()) {
        return response()->json([
            "success" => false,
            "error" => $validator->errors()
        ],400);
    }

    $asiento->update($validator->validated());

    return response()->json([
        "success" => true,
        "message" => "Asiento actualizado correctamente",
        "data" => $asiento
    ],200);
}

     public function destroy(string $id){
        $asiento = Asientos::find($id);
        if(!$asiento){
            return response()->json([
                "success" => false,
                "message" => "Asiento no encontrado"
            ],404);
        }
        $asiento->delete();
        return response()->json([
            "success" => true,
            "message" => "Asiento eliminado correctamente"
        ],200);
     }
}
    