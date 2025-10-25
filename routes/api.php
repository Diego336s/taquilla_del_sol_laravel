<?php

use App\Http\Controllers\AdministradoresController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AsientosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("login/cliente", [ClientesController::class, "login"]);
Route::post("registrar/cliente", [ClientesController::class, "store"]);

Route::middleware(["auth:sanctum"])->group(function () {
    //Acesso Clientes
    Route::middleware("ability:Cliente")->group(function () {
        Route::get("me/cliente", [ClientesController::class, "me"]);
        Route::post("logout/cliente", [ClientesController::class, "logout"]);
    });
});

//Clientes

Route::get("listarClientes", [ClientesController::class, "index"]);

Route::put("actualizarCliente/{id}", [ClientesController::class, "update"]);
Route::delete("eliminarCliente/{id}", [ClientesController::class, "destroy"]);
Route::put("cambiarClave/{id}", [ClientesController::class, "cambiarClave"]);

//Administradores
Route::get("listarAdministradores", [AdministradoresController::class, "index"]);
Route::post("registrarAdministradores", [AdministradoresController::class, "store"]);
Route::put("actualizarAdministradores/{id}", [AdministradoresController::class, "update"]);
Route::put("cambiarClave/{id}", [AdministradoresController::class, "cambiarClave"]);

//Empresas
Route::get("listarEmpresas", [EmpresaController::class, "index"]);
Route::post("registrarEmpresa", [EmpresaController::class, "store"]);
Route::put("actualizarEmpresa/{id}", [EmpresaController::class, "update"]);
Route::delete("eliminarEmpresa/{id}", [EmpresaController::class, "destroy"]);
Route::put("cambiarClave/{id}", [EmpresaController::class, "cambiarClave"]);

//Categorias
Route::get("listarCategorias", [CategoriasController::class, "index"]);
Route::post("registrarCategoria", [CategoriasController::class, "store"]);
Route::put("actualizarCategoria/{id}", [CategoriasController::class, "update"]);
Route::delete("eliminarCategoria/{id}", [CategoriasController::class, "destroy"]);

//Eventos
Route::get("listarEventos", [EventosController::class, "index"]);
Route::post("registrarEventos", [EventosController::class, "store"]);
Route::put("actualizarEventos/{id}", [EventosController::class, "update"]);
Route::delete("eliminarEventos/{id}", [EventosController::class, "destroy"]);

//Tickets
Route::get("listarTickets", [TicketController::class, "index"]);
Route::post("registrarTickets", [TicketController::class, "store"]);
Route::put("actualizarTickets/{id}", [TicketController::class, "update"]);
Route::delete("eliminarTickets/{id}", [TicketController::class, "destroy"]);

//Pagos
Route::get("listarPagos", [PagosController::class, "index"]);
Route::post("registrarPagos", [PagosController::class, "store"]);
Route::put("actualizarPagos/{id}", [PagosController::class, "update"]);
Route::delete("eliminarPagos/{id}", [PagosController::class, "destroy"]);

//Asientos
Route::get("listarAsientos", [AsientosController::class, "index"]);
Route::post("registrarAsientos", [AsientosController::class, "store"]);
Route::get("mostrarAsiento/{id}", [AsientosController::class, "show"]);
Route::put("actualizarAsientos/{id}", [AsientosController::class, "update"]);
Route::delete("eliminarAsientos/{id}", [AsientosController::class, "destroy"]);
