<?php

namespace App\Http\Controllers;

use App\Models\Eventos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventosController extends Controller
{
    public function index()
    {
        $eventos = Eventos::all();
        return response()->json($eventos, 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'        => 'required|string|max:200',
            'descripcion'   => 'nullable|string',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required|string|size:8',
            'hora_final'    => 'required|string|size:8',
            'imagen'        => 'nullable|image|max:2048',
            'estado'        => 'required|in:activo,pendiente,cancelado,finalizado',
            'empresa_id'    => 'required|integer|exists:empresas,id',
            'categoria_id'  => 'required|integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validated();
        $imagen_file = $request->file('imagen');

        // 1. Separar el archivo de imagen de los datos para la creación inicial
        if ($imagen_file) {
            unset($validator_datos['imagen']);
        }

        // 2. Crear el evento en la DB (sin la ruta de la imagen)
        $eventos = Eventos::create($validator_datos);

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
 for ($i=0; $i < 270; $i++) { 
    
 }
        
        return response()->json([
            'success' => true,
            'message' => "Evento creado correctamente",
            'data' => $eventos
        ], 201);
    }


    public function show(string $id)
    {
        $eventos = Eventos::find($id);
        if (!$eventos) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }
        return response()->json($eventos);
    }
    public function update(Request $request, string $id)
    {
        $eventos = Eventos::find($id);

        if (!$eventos) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo'        => 'string|max:200',
            'descripcion'   => 'nullable|string',
            'fecha'         => 'date',
            'hora_inicio'   => 'string|size:8',
            'hora_final'    => 'string|size:8',
            'imagen'        => 'nullable|image|max:2048',
            'estado'        => 'in:activo,pendiente,cancelado,finalizado',
            'empresa_id'    => 'integer|exists:empresas,id',
            'categoria_id'  => 'integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validator_datos = $validator->validated();
        $imagen_file = $request->file('imagen');

        // Separamos la imagen para procesarla por separado
        if (isset($validator_datos['imagen'])) {
            unset($validator_datos['imagen']);
        }

        // LÓGICA PARA ACTUALIZACIÓN DE IMAGEN
        if ($imagen_file) {

            // 1. ELIMINAR la imagen anterior
            if ($eventos->imagen) {
                $ruta_relativa_a_disco = str_replace('/storage/', '', $eventos->imagen);
                if (Storage::disk('public')->exists($ruta_relativa_a_disco)) {
                    Storage::disk('public')->delete($ruta_relativa_a_disco);
                }
            }

            // 2. Preparar la nueva imagen y carpeta
            $file = $imagen_file;

            // Usar el nuevo título del request, o el título actual del evento si no se cambia
            $titulo = $request->input('titulo', $eventos->titulo);

            // Generar la subcarpeta
            $carpeta_evento = Str::slug($titulo);
            $extension = $file->getClientOriginalExtension();

            // Nombre de archivo: [slug]-[id].[ext]
            $nombre_archivo = $carpeta_evento . '-' . $eventos->id . '.' . $extension;

            //  Prefijamos la carpeta dinámica con 'eventos/'
            $ruta_relativa = Storage::disk('public')->putFileAs(
                'eventos/' . $carpeta_evento, // DIRECTORIO FINAL: eventos/titanic
                $file,
                $nombre_archivo
            );

            // 3. Añadir la nueva URL al array de datos para la actualización
            $validator_datos['imagen'] = Storage::url($ruta_relativa);
        }

        // 4. Actualizar el evento con todos los datos validados y la nueva ruta de imagen
        $eventos->update($validator_datos);

        return response()->json($eventos);
    }

    public function destroy(string $id)
    {
        $eventos = Eventos::find($id);

        if (!$eventos) {
            return response()->json(['message' => 'evento no encontrado'], 404);
        }

        // LÓGICA PARA ELIMINAR LA IMAGEN Y SU CARPETA ASOCIADA
        if ($eventos->imagen) {
            // 1. Extraer la ruta relativa al disco 'public'.
            $ruta_relativa_a_disco = str_replace('/storage/', '', $eventos->imagen);

            // 2. Obtener la ruta del directorio padre.
            $ruta_carpeta = dirname($ruta_relativa_a_disco);

            // 3. Eliminar todo el directorio y su contenido (el archivo de imagen).
            if (Storage::disk('public')->exists($ruta_carpeta)) {
                Storage::disk('public')->deleteDirectory($ruta_carpeta);
            }
        }

        // 4. Eliminar el registro del evento de la base de datos.
        $eventos->delete();

        return response()->json(['message' => 'Evento eliminado correctamente']);
    }
}
