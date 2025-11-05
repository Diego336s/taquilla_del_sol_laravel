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
        'estado',
        'empresa_id',
        'categoria_id',
    ];
    public function categoria()
    {
        return $this->belongsTo(categorias::class, 'id');
    }

    public function asientosEventos()
    {
        return $this->hasMany(asientosEventos::class, 'id');
    }
    
    public function empresa()
    {
        return $this->belongsTo(empresas::class, "id");
    }
}
