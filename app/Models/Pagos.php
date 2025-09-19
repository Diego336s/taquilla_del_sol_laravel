<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $fillable = [
        "ticked_id",
        "metodo_pago",
        "monto",
        "fecha_pago",
        "estado",
    ];
}
