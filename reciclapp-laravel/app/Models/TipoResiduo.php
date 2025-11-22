<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class TipoResiduo extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'tipos_residuos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
    ];
}
