<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class Premio extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'premios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'puntos_necesarios',
        'stock',
        'estado',
        'imagen',
    ];
}
