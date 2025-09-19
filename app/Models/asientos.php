<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class asientos extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable =[
    "fila",
    "numero",
    "disponible"
    ];
}

