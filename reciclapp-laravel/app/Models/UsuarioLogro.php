<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class UsuarioLogro extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'usuario_logros_obtenidos';

    protected $fillable = [
        'id_usuario',
        'id_logro',
        'fecha_obtencion',
    ];
}
