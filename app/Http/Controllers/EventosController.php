<?php

namespace App\Http\Controllers;

use App\Models\Asientos;
use App\Models\asientosEventos;
use App\Models\Eventos;
use App\Models\preciosEvento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;



class EventosController extends Controller
{
    public function index()
    {
        $eventos = Eventos::all();

        if (!$eventos || $eventos->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No hay eventos vigentes"
            ]);
        }

        return response()->json([
            "success" => true,
            "eventos" =>  $eventos
        ], 200);
    }


    //listar datos de un solo evento id
    public function evento(string $id)
    {
        $evento = Eventos::with('categoria', 'empresa')->find($id);

        if (!$evento) {
            return response()->json([
                "success" => false,
                "message" => "Evento no encontrado"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "evento" =>  $evento
        ], 200);
    }

    public function cambioDeEstadoDelEvento(Request $request, $id)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            "estado" => "required|in:activo,pendiente,cancelado,finalizado"
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error de validaciones en el servidor.",
                "error" => $validator->errors()
            ], 400);
        }

        try {
            if (Eventos::where("fecha", $request->fecha)
                ->where("estado", "activo")
                ->exists()
            ) {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    "message" => "Ya existe un evento activo en esta fecha."
                ]);
            }
            $evento = Eventos::find($id);
            if (!$evento) {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    'message' => 'Evento no encontrado'
                ], 400);
            }

            if ($evento->estado === "activo") {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    'message' => 'El evento ya esta activo.'
                ], 400);
            }


            if ($request->estado === "activo") {
                $precio_id = 0;
                for ($i = 1; $i <= 270; $i++) {
                    $asiento = Asientos::find($i);
                    switch ($asiento->ubicacion_id) {
                        case 1:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 1)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;
                        case 2:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 2)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;
                        case 3:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 3)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;
                        case 4:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 4)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;
                        case 5:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 5)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;

                        case 6:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 6)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;

                        case 7:
                            $precioEvento = preciosEvento::where("evento_id", $id)
                                ->where("ubicacion_id", 7)
                                ->first();
                            if (!$precioEvento) {
                                DB::rollBack();
                                return response()->json([
                                    "success" => false,
                                    "message" => "No se encontró precio para la ubicación ID: {$asiento->ubicacion_id}"
                                ], 400);
                            }
                            $precio_id = $precioEvento->id;
                            break;

                        default:
                            DB::rollBack();
                            return response()->json([
                                "success" => false,
                                "message" => "Error al crear los asientos para el evento $evento->titulo."
                            ]);
                            break;
                    }

                    asientosEventos::create([
                        "evento_id" => $id,
                        "asiento_id" => $asiento->id,
                        "disponible" => true,
                        "precio_id" => $precio_id
                    ]);
                }
            }
            $evento->update($validator->validated());
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "El evento $evento->titulo se ha aceptado correctamente."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error al cambiar el estado del evento: " . $e->getMessage()
            ]);
        }
    }

    public function eventosDisponibles()
    {
        $eventos = Eventos::with('categoria')
            ->where('estado', 'activo')
            ->get();


        if (!$eventos || $eventos->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No hay eventos vigentes"
            ]);
        }

        return response()->json([
            "success" => true,
            "eventos" =>  $eventos
        ], 200);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        $validacionParaEvento = Validator::make($request->all(), [
            'titulo'        => 'required|string|max:200',
            'descripcion'   => 'required|string',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required|string',
            'hora_final'    => 'required|string',
            'imagen'        => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'estado'        => 'required|in:activo,pendiente,cancelado,finalizado',
            'empresa_id'    => 'required|integer|exists:empresas,id',
            'categoria_id'  => 'required|integer|exists:categorias,id',
        ]);

        if ($validacionParaEvento->fails()) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error de validaciones campos evento en el servidor.",
                "error" =>  $validacionParaEvento->errors()
            ], 400);
        }


        $validacionParaPrecios = Validator::make($request->all(), [
            "precioPrimerPiso" => "required|integer",
            "precioSegundoPiso" => "required|integer",
            "precioGeneral" => "required|integer"
        ]);
        if ($validacionParaPrecios->fails()) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error de validaciones precios en el servidor.",
                "error" =>  $validacionParaPrecios->errors()
            ]);
        }
        try {

            if (Eventos::where("fecha", $request->fecha)
                ->where("estado", "activo")
                ->exists()
            ) {
                return response()->json([
                    "success" => false,
                    "message" => "Ya existe un evento registrado en esta fecha."
                ]);
            }

            $validator_datos = $validacionParaEvento->validated();
            $imagen_file = $request->file('imagen');

            // 1. Separar el archivo de imagen de los datos para la creación inicial
            if ($imagen_file) {
                unset($validator_datos['imagen']);
            }

            // 2. Crear el evento en la DB (sin la ruta de la imagen)
            $eventos = Eventos::create($validator_datos);
            $idEventoCreado = $eventos->id;
            // 3. Procesar y guardar la imagen dentro de la carpeta 'eventos/{slug}'
            if ($imagen_file) {
                $file = $imagen_file;
                $titulo = $eventos->titulo;

                // Generar la subcarpeta
                $carpeta_evento = Str::slug($titulo);
                $extension = $file->getClientOriginalExtension();

                // Nombre de archivo: [slug]-[id].[ext]
                $nombre_archivo = $carpeta_evento . '-' . $eventos->id . '.' . $extension;

                //Prefijamos la carpeta dinámica con 'eventos/'
                $ruta_relativa = Storage::disk('public')->putFileAs(
                    'eventos/' . $carpeta_evento, // DIRECTORIO FINAL: eventos/titanic
                    $file,
                    $nombre_archivo
                );

                // 4. Actualizar el registro del evento con la ruta pública
                $eventos->imagen = Storage::url($ruta_relativa);
                $eventos->save();
                $eventos->refresh();
            }


            for ($i = 1; $i <= 7; $i++) {
                if ($i === 1) {
                    preciosEvento::create([
                        "evento_id" => $idEventoCreado,
                        "ubicacion_id" => $i,
                        "precio" => $request->precioGeneral
                    ]);
                } else if ($i === 2 || $i === 3 || $i === 7) {
                    preciosEvento::create([
                        "evento_id" => $idEventoCreado,
                        "ubicacion_id" => $i,
                        "precio" => $request->precioPrimerPiso
                    ]);
                } else if ($i === 4 || $i === 5 || $i === 6) {
                    preciosEvento::create([
                        "evento_id" => $idEventoCreado,
                        "ubicacion_id" => $i,
                        "precio" => $request->precioSegundoPiso
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Error al crear la solicitud del evento " . $e->getMessage(),
                'data' => $eventos
            ], 200);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Solicitud del evento $request->titulo creada correctamente",
            'data' => $eventos
        ], 200);
    }

    public function eventosPorEmpresa($id)
    {
        $eventos = Eventos::where("empresa_id", $id)->get();

        if ($eventos->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "No hay eventos para esta empresa."
            ]);
        }

        return response()->json([
            "success" => true,
            "eventos" => $eventos
        ], 200);
    }

    public function show(string $id)
    {
        $eventos = Eventos::find($id);
        if (!$eventos) {
            return response()->json(['message' => 'Evento no encontrado']);
        }
        return response()->json($eventos);
    }

    public function update(Request $request, string $id)
    {


        $evento = Eventos::find($id);

        if (!$evento) {
            return response()->json(['success' => false, 'message' => 'Evento no encontrado']);
        }

        // 🔹 Validación principal (imagen solo requerida si cambia)
        $validacionEvento = Validator::make($request->all(), [
            'titulo'        => 'required|string|max:200',
            'descripcion'   => 'required|string',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required|string',
            'hora_final'    => 'required|string',
            'imagen'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'estado'        => 'required|in:activo,pendiente,cancelado,finalizado',
            'empresa_id'    => 'required|integer|exists:empresas,id',
            'categoria_id'  => 'required|integer|exists:categorias,id',
        ]);

        if ($validacionEvento->fails()) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error de validaciones campos evento.",
                "error" => $validacionEvento->errors()
            ], 400);
        }

        // 🔹 Validación de precios
        $validacionPrecios = Validator::make($request->all(), [
            "precioPrimerPiso" => "required|integer|min:0",
            "precioSegundoPiso" => "required|integer|min:0",
            "precioGeneral" => "required|integer|min:0"
        ]);

        if ($validacionPrecios->fails()) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error de validaciones en precios.",
                "error" => $validacionPrecios->errors()
            ]);
        }
        DB::beginTransaction();
        // 🔹 Validar que no haya otro evento activo ese mismo día
        if (
            Eventos::where("fecha", $request->fecha)
            ->where("estado", "activo")
            ->where("id", "!=", $id)
            ->exists()
        ) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Ya existe un evento activo registrado en esta fecha."
            ]);
        }

        try {

            $datos = $validacionEvento->validated();

            // 🟧 PROCESAR IMAGEN SI SE ENVÍA
            if ($request->hasFile("imagen")) {

                // Eliminar imagen anterior
                if ($evento->imagen) {
                    $rutaRelativa = str_replace('/storage/', '', $evento->imagen);
                    Storage::disk('public')->delete($rutaRelativa);
                }

                // Guardar imagen nueva
                $file = $request->file("imagen");
                $carpeta = Str::slug($request->titulo);
                $nombre = $carpeta . "-" . $evento->id . "." . $file->getClientOriginalExtension();

                $ruta = Storage::disk("public")->putFileAs(
                    "eventos/" . $carpeta,
                    $file,
                    $nombre
                );

                $datos["imagen"] = Storage::url($ruta);
            }

            // 🟧 Actualizar evento
            $evento->update($datos);

            // 🟧 Actualizar precios
            $precios = DB::table("precios_eventos")
                ->where("evento_id", $id)
                ->get();

            foreach ($precios as $p) {
                switch ($p->ubicacion_id) {

                    case 1: // General
                        preciosEvento::where("id", $p->id)
                            ->update(["precio" => $request->precioGeneral]);
                        break;

                    case 2:
                    case 3:
                    case 7: // Primer piso
                        preciosEvento::where("id", $p->id)
                            ->update(["precio" => $request->precioPrimerPiso]);
                        break;

                    case 4:
                    case 5:
                    case 6: // Segundo piso
                        preciosEvento::where("id", $p->id)
                            ->update(["precio" => $request->precioSegundoPiso]);
                        break;
                }
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "Evento actualizado $request->titulo correctamente.",
                "evento"  => $evento
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Error al actualizar el evento.",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
{
    DB::beginTransaction();

    try {

        $evento = Eventos::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // 🔹 1. Eliminar precios relacionados (tabla precios_eventos)
        DB::table("precios_eventos")
            ->where("evento_id", $id)
            ->delete();

        // 🔹 2. Eliminar la imagen y su carpeta asociada
        if ($evento->imagen) {

            // Ruta relativa dentro de /storage/app/public
            $ruta_relativa = str_replace('/storage/', '', $evento->imagen);

            // Carpeta donde está la imagen
            $carpeta = dirname($ruta_relativa);

            // Eliminar carpeta completa si existe
            if (Storage::disk('public')->exists($carpeta)) {
                Storage::disk('public')->deleteDirectory($carpeta);
            }
        }

        // 🔹 3. Eliminar el evento
        $evento->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Evento eliminado correctamente"
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el evento',
            'error'   => $e->getMessage()
        ], 500);
    }
}


    public function proximaFuncion($idCliente)
    {
        $hoy = now()->toDateString();

        // 1️⃣ Obtener el evento más próximo del cliente
        $evento = DB::table('clientes')
            ->join('tickets', 'tickets.cliente_id', '=', 'clientes.id')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->where('clientes.id', $idCliente)
            ->whereDate('eventos.fecha', '>=', $hoy)
            ->orderBy('eventos.fecha', 'asc')
            ->select(
                'eventos.id as evento_id',
                'eventos.titulo',
                'eventos.fecha as fecha_evento',
                'eventos.hora_inicio',
                'eventos.hora_final'
            )
            ->first();

        if (!$evento) {
            return response()->json([
                "success" => false,
                "message" => "No tienes funciones próximas"
            ]);
        }

        // 2️⃣ Obtener TODOS los tickets del cliente para ese evento
        $tickets = DB::table('tickets')
            ->where('cliente_id', $idCliente)
            ->where('evento_id',  $evento->evento_id)
            ->get();

        $idsTickets = $tickets->pluck('id')->toArray();

        // 3️⃣ Obtener todos los asientos reservados en esos tickets
        $asientos = DB::table('reserva_asientos')
            ->join('asientos_eventos', 'asientos_eventos.id', '=', 'reserva_asientos.asiento_evento_id')
            ->join('asientos', 'asientos.id', '=', 'asientos_eventos.asiento_id')
            ->join('ubicacion_asientos', 'ubicacion_asientos.id', '=', 'asientos.ubicacion_id')
            ->join('precios_eventos', 'precios_eventos.id', '=', 'asientos_eventos.precio_id')
            ->whereIn('reserva_asientos.ticket_id', $idsTickets)
            ->orderBy('asientos.fila')
            ->orderBy('asientos.numero')
            ->select(
                'asientos.fila',
                'asientos.numero',
                'ubicacion_asientos.ubicacion',
                'precios_eventos.precio as precio_asiento'
            )
            ->get();

        // 4️⃣ Obtener datos del cliente (una sola vez)
        $cliente = DB::table('clientes')->where('id', $idCliente)->first();

        return response()->json([
            "success" => true,
            "proxima_funcion" => [
                "evento" => [
                    "titulo"       => $evento->titulo,
                    "fecha_evento" => $evento->fecha_evento,
                    "hora_inicio"  => $evento->hora_inicio,
                    "hora_final"   => $evento->hora_final,
                ],
                "cliente" => [
                    "nombre"     => $cliente->nombre,
                    "apellido"   => $cliente->apellido,
                    "correo"     => $cliente->correo,
                    "documento"  => $cliente->documento,
                    "telefono"   => $cliente->telefono,
                ],
                "tickets" => $tickets,         // TODOS los tickets del evento
                "asientos" => $asientos         // TODOS los asientos asignados
            ]
        ]);
    }

    public function contarFuncionesProximas($idCliente)
    {
        $hoy = now()->toDateString(); // Fecha actual

        $cantidad = DB::table('tickets')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->where('tickets.cliente_id', $idCliente)
            ->whereDate('eventos.fecha', '>=', $hoy)
            ->distinct('tickets.evento_id') // <<< SOLO UN EVENTO ÚNICO
            ->count('tickets.evento_id');

        return response()->json([
            "success" => true,
            "proximas_funciones" => $cantidad
        ]);
    }

    public function contarFuncionesVistas($idCliente)
    {
        $hoy = now()->toDateString(); // Fecha actual

        $cantidad = DB::table('tickets')
            ->join('eventos', 'eventos.id', '=', 'tickets.evento_id')
            ->where('tickets.cliente_id', $idCliente)
            ->whereDate('eventos.fecha', '<', $hoy) // FECHA PASADA = función vista
            ->distinct('tickets.evento_id')         // NO duplicar
            ->count('tickets.evento_id');

        return response()->json([
            "success" => true,
            "funciones_vistas" => $cantidad
        ]);
    }

    public function contadorObrasActivas($idEmpresa)
    {
        $cantidad = DB::table('eventos')
            ->where('empresa_id', $idEmpresa)
            ->where('estado', 'activo')
            ->count();

        return response()->json([
            "success" => true,
            "obras_activas" => $cantidad
        ]);
    }


    public function obtenerEventoCompleto($id)
    {
        // 1️⃣ Obtener datos del evento + categoría
        $evento = DB::table("eventos")
            ->join("categorias", "eventos.categoria_id", "=", "categorias.id")
            ->where("eventos.id", $id)
            ->select(
                "eventos.*",
                "categorias.nombre as categoria"
            )
            ->first();

        if (!$evento) {
            return response()->json([
                "success" => false,
                "message" => "Evento no encontrado"
            ]);
        }

        // 2️⃣ Precios agrupados por ubicacion_id y sin duplicados
        $precios = DB::table("precios_eventos")
            ->where("evento_id", $id)
            ->select("ubicacion_id", "precio")
            ->distinct()                 // ← evita filas duplicadas
            ->orderBy("ubicacion_id")    // para orden ordenado
            ->get();

        $precioPrimerPiso = 0;
        $precioSegundoPiso = 0;
        $precioGeneral = 0;
        foreach ($precios as $item) {
            switch ($item->ubicacion_id) {
                case 1:
                    $precioGeneral = $item->precio;
                    break;
                case 2:
                    $precioPrimerPiso = $item->precio;
                    break;
                case 4:
                    $precioSegundoPiso = $item->precio;
                    break;
                default:

                    break;
            }
        }


        // 3️⃣ Agregar precios al evento en un array
        $evento->precioPrimerPiso = $precioPrimerPiso;
        $evento->precioSegundoPiso = $precioSegundoPiso;
        $evento->precioGeneral = $precioGeneral;

        return response()->json([
            "success" => true,
            "evento" => $evento,

        ]);
    }
}
