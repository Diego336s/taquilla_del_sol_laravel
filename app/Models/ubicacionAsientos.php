<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ubicacionAsientos extends Model
{
    protected $fillable = [
        "ubicacion"
    ];

    public function asientos()
    {
        return $this->hasMany(asientosEventos::class, "ubicacion_id", "id");
    }

       public function precios()
    {
        return $this->belongsToMany(preciosEvento::class, "ubicacion_id", "id");
    }
}
