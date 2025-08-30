<?php

namespace App\Http\Controllers;

use App\Models\categorias;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    public function index(){

        $categorias = categorias::all();
        return response()->json($categorias);
    }

    public function store(Request $request){

        $Validator = Validator::make($request->all(),[
            'nombre'=>'required/string/max:100',
        ]);
        if($Validator->fails()){
            return response()->json($Validator->errors(), 400);
        }
        $categorias = categorias::create($Validator->validated());
        return response()->json($categorias,201);

    }

    public function show(string $id){
        $categorias = categorias::find($id);
        if(!$categorias){
            return response()->json(['message'=>'categoria no encontrada'],400);
        }
        return response()->json($categorias);

    }
    public function update(Request $request, string $id){
        $categorias = categorias::find($id);
        if(!$categorias){
            return response()->json(['message'=>'categoria no encontrada'], 400);

        }
        $Validator = Validator::make($request->all(),[
            'nombre'=>'required/string/max:100',
        ]);
        if($Validator->fails()){
            return response()->json($Validator->errors(), 400);
        }
        $categorias->update($Validator->Validated());
        return response()->json($categorias);
    }
    public function destroy(string $id){
        $categorias = categorias::find($id);
        if(!$categorias){
            return response()->json(['message'=> 'categoria no encontrada'],400);
        }
        $categorias->delete();
        return response()->json(['message'=>'categoria eliminada']);
    }


}
