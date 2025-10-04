<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categorias extends Model
{
    protected $fillable = [
        'nombre',
    ];

    public function categorias(){
        return $this->hasMany(categorias::class, 'id');
    }
}
