<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ReciclApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login_register_style.css') }}">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-shell">
            <div class="auth-info auth-info-register">
                <div class="auth-info-logo">
                    <i class="fas fa-recycle"></i>
                    <span>ReciclApp</span>
                </div>
                <h1>Comienza tu ruta de reciclaje.</h1>
                <p>
                    Crea una cuenta para registrar tus entregas, ganar puntos y sumarte a una comunidad que recicla
                    de forma inteligente.
                </p>
                <ul class="auth-info-list">
                    <li><i class="fas fa-seedling"></i> Visualiza cómo tus acciones reducen el impacto ambiental.</li>
                    <li><i class="fas fa-users"></i> Forma parte de una red de recicladores responsables.</li>
                    <li><i class="fas fa-trophy"></i> Desbloquea logros y recompensas por mantener el hábito.</li>
                </ul>

                <div class="auth-info-badges">
                    <span class="auth-badge"><i class="fas fa-shield-alt"></i> Datos protegidos</span>
                    <span class="auth-badge"><i class="fas fa-star"></i> Cuenta gratuita</span>
                    <span class="auth-badge"><i class="fas fa-mobile-alt"></i> Accede desde cualquier dispositivo</span>
                </div>
            </div>

            <div class="auth-card">
                <div class="auth-header">
                    <h2>Crea tu cuenta</h2>
                    <div class="auth-stepper">
                        <div class="auth-stepper-item is-active" data-step="1">
                            <span class="auth-stepper-dot">1</span>
                            <span class="auth-stepper-label">Datos personales</span>
                        </div>
                        <div class="auth-stepper-separator"></div>
                        <div class="auth-stepper-item" data-step="2">
                            <span class="auth-stepper-dot">2</span>
                            <span class="auth-stepper-label">Seguridad</span>
                        </div>
                    </div>
                </div>

                <form class="auth-form" id="register-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-error">{{ $errors->first() }}</div>
                    @endif

                    <div class="auth-steps" data-current-step="1">
                        <div class="auth-step is-active" data-step="1">
                            <div class="form-group">
                                <label for="id_usuario">ID / Cédula *</label>
                                <input type="text" name="id_usuario" value="{{ old('id_usuario') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido">Apellido *</label>
                                <input type="text" name="apellido" value="{{ old('apellido') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Correo *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <button type="button" class="auth-btn auth-btn-next" data-next-step="2">Siguiente</button>
                        </div>

                        <div class="auth-step" data-step="2">
                            <div class="form-group">
                                <label for="password">Contraseña *</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirmar Contraseña *</label>
                                <input type="password" name="password_confirmation" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                            </div>
                            <div class="auth-step-actions">
                                <button type="button" class="auth-btn auth-btn-secondary auth-btn-prev" data-prev-step="1">Atrás</button>
                                <button type="submit" class="auth-btn">Registrarse</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="auth-footer">
                    <span>¿Ya tienes cuenta?</span>
                    <a href="{{ route('login') }}">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var stepsContainer = document.querySelector('.auth-steps');
            if (!stepsContainer) {
                return;
            }

            var steps = Array.prototype.slice.call(document.querySelectorAll('.auth-step'));
            var stepperItems = Array.prototype.slice.call(document.querySelectorAll('.auth-stepper-item'));

            function setActiveStep(stepNumber) {
                stepsContainer.setAttribute('data-current-step', String(stepNumber));

                steps.forEach(function (step) {
                    var stepValue = step.getAttribute('data-step');
                    if (stepValue === String(stepNumber)) {
                        step.classList.add('is-active');
                    } else {
                        step.classList.remove('is-active');
                    }
                });

                stepperItems.forEach(function (item) {
                    var itemStep = item.getAttribute('data-step');
                    if (itemStep === String(stepNumber)) {
                        item.classList.add('is-active');
                    } else {
                        item.classList.remove('is-active');
                    }

                    var numericStep = parseInt(itemStep || '0', 10);
                    if (!isNaN(numericStep) && numericStep < stepNumber) {
                        item.classList.add('is-completed');
                    } else {
                        item.classList.remove('is-completed');
                    }
                });
            }

            function validateStep(stepNumber) {
                if (stepNumber !== 1) {
                    return true;
                }
                var requiredSelectors = ['input[name="id_usuario"]', 'input[name="nombre"]', 'input[name="apellido"]', 'input[name="email"]'];
                var valid = true;
                requiredSelectors.forEach(function (selector) {
                    var input = document.querySelector(selector);
                    if (!input) {
                        return;
                    }
                    if (!input.value || String(input.value).trim() === '') {
                        valid = false;
                        input.classList.add('auth-input-error');
                    } else {
                        input.classList.remove('auth-input-error');
                    }
                });
                return valid;
            }

            document.addEventListener('click', function (event) {
                var target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }

                if (target.classList.contains('auth-btn-next')) {
                    var next = target.getAttribute('data-next-step');
                    var nextStep = parseInt(next || '0', 10);
                    if (!nextStep || !validateStep(1)) {
                        return;
                    }
                    setActiveStep(nextStep);
                }

                if (target.classList.contains('auth-btn-prev')) {
                    var prev = target.getAttribute('data-prev-step');
                    var prevStep = parseInt(prev || '0', 10);
                    if (!prevStep) {
                        return;
                    }
                    setActiveStep(prevStep);
                }
            });
        });
    </script>
</body>
</html>
