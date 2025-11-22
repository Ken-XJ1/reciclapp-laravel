<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class Logro extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'logros';

    protected $fillable = [
        'nombre',
        'descripcion',
        'puntos_requeridos',
        'icono',
    ];
}
