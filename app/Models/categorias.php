<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categorias extends Model
{
    protected $fillable = [
        'nombre',
    ];

    public function eventos(){
        return $this->hasMany(Eventos::class, 'categoria_id');
    }
}
