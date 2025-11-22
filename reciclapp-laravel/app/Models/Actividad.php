<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class Actividad extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'actividades';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha',
        'lugar',
        'estado', // activa, finalizada, cancelada
    ];
}
