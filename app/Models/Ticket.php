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
    public function pago()
    {
        return $this->hasOne(Pagos::class, "ticket_id");
    }

    public function cliente()
    {
        return $this->hasOne(clientes::class, "id");
    }
    public function reservaAsiento()
    {
        return $this->belongsToMany(reservaAsientos::class, 'ticket_id');
    }

    
}
