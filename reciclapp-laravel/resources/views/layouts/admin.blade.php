<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReciclApp - @yield('title', 'Admin')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    {{-- Estilos exclusivos del panel admin --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-recycle"></i>
                <span>ReciclApp - Admin</span>
            </div>
            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('home') }}" class="btn-login">Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} ReciclApp. Todos los derechos reservados.</p>
        </div>
    </footer>

    {{-- JS de Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    @stack('scripts')
</body>
</html>
