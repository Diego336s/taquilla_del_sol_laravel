<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Empresas extends Model
{
    use HasApiTokens, Notifiable;
    use HasFactory;    
    

    protected$fillable = [
            'nombre_empresa',
            'nit',
            'representante_legal',
            'documento_representante',
            'nombre_contacto',
            'telefono',
            'correo',
            'clave', 
    ];

}
