<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class clientes extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        "nombre",
        "apellido",
        "documento",
        "fecha_nacimiento",
        "telefono",
        "sexo",
        "correo",
        "clave",
        'codigo_recuperacion'
    ];
    public function ticket()
    {
        return $this->hasOne(Ticket::class, "cliente_id");
    }
}
