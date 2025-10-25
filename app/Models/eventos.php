<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha',
        "hora_inicio",
        "hora_final",  
        'capacidad',
        'estado',
        'empresa_id',
        'categoria_id',
    ];
}
