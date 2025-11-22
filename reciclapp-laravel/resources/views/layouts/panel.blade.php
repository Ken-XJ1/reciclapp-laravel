<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReciclApp - Panel de Usuario</title>

    <link rel="stylesheet" href="{{ asset('css/login_register_style.css') }}">
    <link rel="icon" href="{{ asset('img/icono.png') }}" type="image/png">
</head>
<body>
    <main>
        @yield('content')
    </main>

    <footer style="text-align:center; margin-top:30px; color:#555;">
        © 2025 ReciclApp - Quibdó, Chocó
    </footer>

    <script src="https://kit.fontawesome.com/a2d9b6a2c4.js" crossorigin="anonymous"></script>
</body>
</html>
