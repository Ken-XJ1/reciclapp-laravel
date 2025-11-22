<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReciclApp - @yield('title', 'Inicio')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    {{-- Estilos generales del proyecto --}}
    <link rel="stylesheet" href="{{ asset('css/panel_flujo_style.css') }}">

    @stack('styles')
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-recycle"></i>
                <span>ReciclApp</span>
            </div>
            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('home') }}" class="nav-link-simple">Inicio</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#about" class="nav-link-simple">Sobre</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#features" class="nav-link-simple">Características</a>
                    </li>

                    @auth
                        <li class="nav-auth">
                            <a href="{{ route('logout') }}" class="btn-login">Cerrar sesión</a>
                        </li>
                    @else
                        <li class="nav-auth">
                            <a href="{{ route('login') }}" class="nav-link-simple">Iniciar sesión</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="btn-login">Registrarse</a>
                        </li>
                    @endauth
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
