<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    protected $fillable = [
        "nombre",
        "apellido",
        "documento",
        "fecha_nacimiento",
        "telefono",
        "correo",
        "clave"
    ];

    public function eventos()
    {
        return $this->hasMany(eventos::class, "cliente_id");
    }
}
