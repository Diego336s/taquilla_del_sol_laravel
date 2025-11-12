<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class asientosEventos extends Model
{
    protected $fillable = [
        "evento_id",
        "asiento_id",
        "disponible",
        "precio_id"
    ];

      public function eventos(){
        return $this->belongsToMany(Eventos::class, "evento_id",'id');
    }
    public function reservaAsientos(){
        return $this->belongsToMany(reservaAsientos::class,  "asiento_evento_id",'id');
    }

    public function precioAsiento(){
        return $this->belongsToMany(preciosEvento::class,  "precio_id",'id');
    }
}
