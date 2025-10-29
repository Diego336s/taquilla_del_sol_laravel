<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $fillable = [
        "ticket_id",
        "metodo_pago",
        "monto",
        "fecha_pago",
        "estado",
    ];
    public function ticket()
    {
        return $this->hasOne(Ticket::class, "id");
    }
}
