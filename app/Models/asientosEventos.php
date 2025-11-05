<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class asientosEventos extends Model
{
    protected $fillable = [
        "evento_id",
        "asiento_id",
        "disponible"
    ];

      public function eventos(){
        return $this->belongsTo(Eventos::class, 'id');
    }
    public function reservaAsientos(){
        return $this->belongsToMany(reservaAsientos::class, 'id');
    }
}
