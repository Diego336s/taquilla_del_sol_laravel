<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservaAsientos extends Model
{
    protected $fillable = [
        "ticket_id",
        "asiento_id"
    ];

    public function ticketS(){
        return $this->belongsToMany(Ticket::class, 'id');
    }
    public function asientos(){
        return $this->belongsToMany(Asientos::class, 'id');
    }
}
