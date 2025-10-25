<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'evento_id',
        'cliente_id',       
        'precio',
        'estado',
        'fecha_compra',
    ];
}
