<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class preciosEvento extends Model
{
    protected $fillable = [
        "evento_id",
        "ubicacion_id",
        "precio"
    ];
    public function ubicacion(){
       return $this->belongsToMany(ubicacionAsientos::class, "ubicacion_id", "id");
    }

       public function asientosEvento(){
       return $this->belongsToMany(asientosEventos::class, "precio_id", "id");
    }

     public function evento(){
       return $this->belongsToMany(Eventos::class, "evento_id", "id");
    }
    
}
