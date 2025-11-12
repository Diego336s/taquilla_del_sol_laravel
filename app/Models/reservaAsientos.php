<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservaAsientos extends Model
{
    protected $fillable = [
        "ticket_id",
        "asiento_evento_id"
    ];

    public function tickets(){
        return $this->belongsToMany(Ticket::class, 'id');
    }
    public function asientosEventos(){
        return $this->belongsToMany(asientosEventos::class, "asiento_evento_id",'id');
    }
}
