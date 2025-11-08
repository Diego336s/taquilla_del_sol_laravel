<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'imagen',
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

    protected function imagen(): Attribute
    {
        return Attribute::make(
            // El valor ($value) es la ruta guardada en la DB (ej: /storage/eventos/titanic/...)
            get: fn (string|null $value) => $value ? url($value) : null,

            // En el terminal para acceder a la ruta public/storage --- php artisan storage:link ----
        );
    }
}
