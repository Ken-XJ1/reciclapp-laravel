<?php

return [
    // Mensajes generales
    'required' => 'El campo :attribute es obligatorio.',
    'email'    => 'Ingresa un correo electrónico válido.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'unique'   => 'El :attribute ya está registrado.',

    'string' => 'El campo :attribute debe ser texto.',

    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],

    'max' => [
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],

    'between' => [
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],

    // Aquí puedes ir agregando más reglas según las necesites...

    // Nombres amigables de campos (esto es lo que ve el usuario)
    'attributes' => [
        'nombre'       => 'nombre del punto',
        'tipo'         => 'tipo de reciclaje',
        'direccion'    => 'dirección',
        'horario'      => 'horario',
        'descripcion'  => 'descripción',
        'descripcion_otro' => 'descripción del problema',
        'punto_id'     => 'punto de reciclaje',

        'email'        => 'correo electrónico',
        'password'     => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
    ],
];
