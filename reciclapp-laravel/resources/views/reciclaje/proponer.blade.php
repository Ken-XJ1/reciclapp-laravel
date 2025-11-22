@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel_flujo_style.css') }}">
@endsection

@section('content')
<div class="flujo-container">
    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('panel.reciclaje') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert-flujo alert-flujo-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert-flujo alert-flujo-error">{{ session('error') }}</div>
    @endif

    <div class="flujo-header" style="text-align: left; margin-bottom: 24px;">
        <div class="flujo-chip">
            <i class="fas fa-map-marker-alt"></i>
            <span>Proponer nuevo punto</span>
        </div>
        <h1>Ayuda a ampliar la red de reciclaje</h1>
        <p>Comparte un lugar donde otras personas puedan llevar materiales reciclables. Puede ser un centro formal, un
            contenedor de tu barrio o un espacio temporal después de un evento.</p>
    </div>

    <div class="landing-two-columns" style="margin-top: 8px;">
        <!-- Columna izquierda: Formulario principal -->
        <div>
            <div class="landing-card">
                <h2 style="margin-top: 0; margin-bottom: 10px;">Datos del punto de reciclaje</h2>
                <p style="margin-top: 0; margin-bottom: 18px; font-size: 0.95rem; color: var(--flujo-text-light);">
                    Completa la información básica del lugar. Mientras más claro sea el detalle, más fácil será ubicarlo y
                    validarlo.
                </p>

                <form action="{{ route('panel.reciclaje.proponer.guardar') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nombre">Nombre o referencia del lugar</label>
                        <input type="text" name="nombre" id="nombre"
                            placeholder="Ej: Parque Central - Feria de libros" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo de materiales disponibles</label>
                        <select name="tipo" id="tipo" required>
                            <option value="">Selecciona el tipo</option>
                            <option value="Plástico" {{ old('tipo') === 'Plástico' ? 'selected' : '' }}>Plástico</option>
                            <option value="Papel" {{ old('tipo') === 'Papel' ? 'selected' : '' }}>Papel</option>
                            <option value="Vidrio" {{ old('tipo') === 'Vidrio' ? 'selected' : '' }}>Vidrio</option>
                            <option value="Orgánico" {{ old('tipo') === 'Orgánico' ? 'selected' : '' }}>Orgánico</option>
                            <option value="Electrónico" {{ old('tipo') === 'Electrónico' ? 'selected' : '' }}>Electrónico</option>
                            <option value="Mixto" {{ old('tipo') === 'Mixto' ? 'selected' : '' }}>Mixto</option>
                        </select>
                        @error('tipo')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección o ubicación aproximada</label>
                        <input type="text" name="direccion" id="direccion"
                            placeholder="Calle, barrio, ciudad o punto de referencia" value="{{ old('direccion') }}" required>
                        @error('direccion')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="horario">Horario recomendado</label>
                        <input type="text" name="horario" id="horario"
                            placeholder="Ej: Disponible desde las 10:00 hasta las 18:00" value="{{ old('horario') }}">
                        @error('horario')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Detalles adicionales (opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="4"
                            placeholder="Ej: tras evento deportivo, festival o feria, materiales separados, etc.">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div
                        style="display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; align-items: center; justify-content: flex-start;">
                        <button type="submit" class="btn-accion-flujo">Enviar propuesta</button>
                        <a href="{{ route('panel.reciclaje') }}" class="btn-volver">
                            <i class="fas fa-arrow-left"></i>
                            <span>Cancelar y volver</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna derecha: Información y recomendaciones -->
        <div>
            <div class="landing-card" style="margin-bottom: 18px; padding-bottom: 0;">
                <div
                    style="border-radius: 1.25rem; overflow: hidden; min-height: 210px; background-size: cover; background-position: center; box-shadow: var(--flujo-box-shadow); background-image: linear-gradient(135deg, rgba(5,150,105,0.3), rgba(20,184,166,0.25)), url('https://images.pexels.com/photos/1000058/pexels-photo-1000058.jpeg?auto=compress&cs=tinysrgb&w=1200');">
                </div>
                <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin: 12px 2px 16px;">
                    
                </p>
            </div>

            <div class="landing-card" style="margin-bottom: 18px;">
                <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-lightbulb" style="color: var(--flujo-primary-color);"></i>
                    Consejos para un buen punto
                </h3>
                <p style="font-size: 0.95rem; color: var(--flujo-text-light); margin-bottom: 10px;">
                    Ten en cuenta estos detalles al proponer un nuevo lugar de reciclaje:
                </p>
                <ul class="landing-list">
                    <li>Verifica que el lugar sea seguro y accesible para otras personas.</li>
                    <li>Indica referencias claras (parques, esquinas, locales cercanos, etc.).</li>
                    <li>Describe qué tipo de materiales suelen acumularse allí.</li>
                    <li>Si es un evento temporal, menciona la fecha o duración aproximada.</li>
                </ul>
            </div>

            <div class="landing-card">
                <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shield-alt" style="color: var(--flujo-secondary-color);"></i>
                    ¿Qué pasa después de enviar tu propuesta?
                </h3>
                <div class="lp-hero-card" id="proponer-flow-card" style="margin-top: 6px;">
                    <div class="lp-hero-card-body">
                        <div class="lp-tip-slide active">
                            <p class="lp-hero-card-copy">
                                1. Revisamos tu propuesta para confirmar que el lugar sea adecuado y seguro para reciclar.
                            </p>
                        </div>
                        <div class="lp-tip-slide">
                            <p class="lp-hero-card-copy">
                                2. Si todo está correcto, el punto puede incorporarse al mapa para que más personas lo
                                encuentren.
                            </p>
                        </div>
                        <div class="lp-tip-slide">
                            <p class="lp-hero-card-copy">
                                3. Tus propuestas ayudan a que la red de reciclaje crezca y sea más útil para tu comunidad.
                            </p>
                        </div>
                    </div>
                    <div class="lp-tip-dots">
                        <span class="lp-tip-dot active"></span>
                        <span class="lp-tip-dot"></span>
                        <span class="lp-tip-dot"></span>
                    </div>
                </div>
                <p class="lp-hero-card-footer-text" style="margin-top: 10px;">
                    Puedes seguir proponiendo nuevos puntos siempre que detectes lugares donde el reciclaje pueda tener
                    impacto.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var card = document.getElementById('proponer-flow-card');
        if (!card) return;

        var slides = card.querySelectorAll('.lp-tip-slide');
        var dots = card.querySelectorAll('.lp-tip-dot');
        if (!slides.length) return;

        var index = 0;
        setInterval(function () {
            slides[index].classList.remove('active');
            if (dots[index]) {
                dots[index].classList.remove('active');
            }

            index = (index + 1) % slides.length;

            slides[index].classList.add('active');
            if (dots[index]) {
                dots[index].classList.add('active');
            }
        }, 5000);
    });
</script>
