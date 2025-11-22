<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'usuarios';

    protected $fillable = [
        'id_usuario',
        'nombre',
        'apellido',
        'email',
        'contrasena_hash',
        'rol',
        'fecha_nacimiento',
        'estado',
        'intentos_fallidos',
        'bloqueado',
        'puntos_acumulados',
    ];

    protected $hidden = [
        'contrasena_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }
}
