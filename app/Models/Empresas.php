<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Empresa extends Model
{
    use HasApiTokens, Notifiable;
    use HasFactory;
    protected $table = 'empresa';
    protected $primaryKey = 'id';

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
