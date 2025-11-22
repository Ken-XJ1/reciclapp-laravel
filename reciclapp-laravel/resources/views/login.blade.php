<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ReciclApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login_register_style.css') }}">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-shell">
            <div class="auth-info">
                <div class="auth-info-logo">
                    <i class="fas fa-recycle"></i>
                    <span>ReciclApp</span>
                </div>
                <h1>Vuelve a conectar con tu impacto.</h1>
                <p>
                    Ingresa a tu cuenta para registrar nuevas entregas, revisar tus puntos acumulados y descubrir
                    nuevas formas de reciclar en tu comunidad.
                </p>
                <ul class="auth-info-list">
                    <li><i class="fas fa-check-circle"></i> Accede a tu panel de reciclaje personalizado.</li>
                    <li><i class="fas fa-map-marker-alt"></i> Consulta puntos de reciclaje y propuestas.</li>
                    <li><i class="fas fa-gift"></i> Sigue de cerca tus recompensas y logros.</li>
                </ul>
            </div>

            <div class="auth-card">
                <div class="auth-header">
                    <h2>Iniciar sesión</h2>
                    <p>Ingresa tus credenciales para acceder a tu panel.</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-error">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" 
                               placeholder="tu@email.com"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="auth-btn">Iniciar Sesión</button>
                </form>

                <div class="auth-footer">
                    <span>¿Aún no tienes cuenta?</span>
                    <a href="{{ route('register') }}">Crear una cuenta</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
