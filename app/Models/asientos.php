<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Asientos extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        "ubicacion",
        "fila",
        "numero"
    ];
     public function reservaAsiento()
    {
        return $this->belongsToMany(reservaAsientos::class, 'asiento_id');
    }
}
