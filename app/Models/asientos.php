<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Asientos extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        "ubicacion_id",
        "fila",
        "numero"
    ];
    public function reservaAsiento()
    {
        return $this->belongsToMany(reservaAsientos::class, 'asiento_id');
    }
    public function ubicaciones()
    {
        return $this->belongsTo(ubicacionAsientos::class, "ubicacion_id", "id");
    }
}
