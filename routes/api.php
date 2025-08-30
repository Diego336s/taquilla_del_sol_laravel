<?php

use App\Models\clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("listarClientes", [clientes::class, "index"]);
Route::post("registrarCliente", [clientes::class, "store"]);
Route::put("actualizarCliente/{id}", [clientes::class, "update"]);
Route::delete("eliminarCliente/{id}", [clientes::class, "destroy"]);
Route::put("cambiarClave/{id}", [clientes::class, "cambiarClave"]);