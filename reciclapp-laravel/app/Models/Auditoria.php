<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class Auditoria extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'auditoria';

    protected $fillable = [
        'accion',
        'usuario',
        'fecha',
        'descripcion',
        'modulo_afectado',
        'resultado',
        'ip',
    ];
}
