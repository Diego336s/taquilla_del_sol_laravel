<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Administradores extends Model
{
     use HasApiTokens, Notifiable;
    protected $fillable = ["nombre", "apellido", "documento", "fecha_nacimiento", "telefono","correo", "clave"];
}
