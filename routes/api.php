<?php

use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\EmpresaController   ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("listarClientes", [ClientesController::class, "index"]);
Route::post("registrarCliente", [ClientesController::class, "store"]);
Route::put("actualizarCliente/{id}", [ClientesController::class, "update"]);
Route::delete("eliminarCliente/{id}", [ClientesController::class, "destroy"]);
Route::put("cambiarClave/{id}", [ClientesController::class, "cambiarClave"]);

Route::get("listarAdministradores", [AdministradoresController::class, "index"]);
Route::post("registrarAdministradores", [AdministradoresController::class, "store"]);
Route::put("actualizarAdministradores/{id}", [AdministradoresController::class, "update"]);
Route::put("cambiarClave/{id}", [AdministradoresController::class, "cambiarClave"]);

Route::get("listarEmpresas", [EmpresaController::class, "index"]);
Route::post("registrarEmpresa", [EmpresaController::class, "store"]);
Route::put("actualizarEmpresa/{id}", [EmpresaController::class, "update"]);
Route::delete("eliminarEmpresa/{id}", [EmpresaController::class, "destroy"]);
Route::put("cambiarClave/{id}", [EmpresaController::class, "cambiarClave"]);