@extends('layouts.app')

@section('title', 'Registrar mi entrega')

@section('content')
    <div class="flujo-container" style="margin-top: 2rem; margin-bottom: 2rem;">
        <div class="flujo-header" style="margin-bottom: 24px; text-align: left;">
            <div class="flujo-chip">
                <i class="fas fa-leaf"></i>
                <span>Registrar mi entrega</span>
            </div>
            <h1>Registra tu aporte al planeta</h1>
            <p>Ingresa los datos de los materiales que has llevado a reciclar para que podamos sumar tu impacto.</p>
        </div>

        @if(session('success'))
            <div class="alert-flujo alert-flujo-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert-flujo alert-flujo-error">{{ session('error') }}</div>
        @endif

        <div class="landing-two-columns" style="margin-top: 8px;">
            <div>
                <form action="{{ route('panel.reciclaje.guardar') }}" method="POST" class="form-section">
                    @csrf

                    <div class="form-group">
                        <label for="tipo_residuo">Tipo de residuo</label>
                        <input type="text" id="tipo_residuo" name="tipo_residuo" value="{{ old('tipo_residuo') }}" required>
                        @error('tipo_residuo')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cantidad">Cantidad (kg)</label>
                        <input type="number" step="0.1" min="0.1" id="cantidad" name="cantidad" value="{{ old('cantidad') }}" required>
                        @error('cantidad')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_recoleccion_usuario">Fecha de la recolección</label>
                        <input type="date" id="fecha_recoleccion_usuario" name="fecha_recoleccion_usuario" value="{{ old('fecha_recoleccion_usuario') }}">
                        @error('fecha_recoleccion_usuario')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ubicacion_recoleccion">Lugar de la recolección (opcional)</label>
                        <input type="text" id="ubicacion_recoleccion" name="ubicacion_recoleccion" value="{{ old('ubicacion_recoleccion') }}" placeholder="Ej: Punto limpio del parque central, supermercado X...">
                        @error('ubicacion_recoleccion')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion_general_materiales">Descripción general de los materiales</label>
                        <textarea id="descripcion_general_materiales" name="descripcion_general_materiales" rows="3" placeholder="Ej: Botellas de plástico, cartón, vidrio limpio...">{{ old('descripcion_general_materiales') }}</textarea>
                        @error('descripcion_general_materiales')
                            <small class="text-red-600">{{ $message }}</small>
                        @enderror
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; align-items: center;">
                        <button type="submit" class="btn-accion-flujo">Registrar entrega</button>
                        <a href="{{ route('panel.reciclaje') }}" class="btn-volver">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                </form>
            </div>

            <div>
                <div class="landing-card" style="margin-bottom: 18px; padding-bottom: 0;">
                    <div
                        style="border-radius: 1.25rem; overflow: hidden; min-height: 220px; background-size: cover; background-position: center; box-shadow: var(--flujo-box-shadow); background-image: linear-gradient(135deg, rgba(5,150,105,0.35), rgba(20,184,166,0.3)), url('https://images.pexels.com/photos/802221/pexels-photo-802221.jpeg?auto=compress&cs=tinysrgb&w=1200');">
                    </div>
                    <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin: 12px 2px 16px;">
                        Imagina que cada bolsa que entregas aquí se transforma en árboles, parques limpios y menos residuos
                        en tu ciudad. Registrar tu entrega es el primer paso.
                    </p>
                </div>

                <div class="landing-card">
                    <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-recycle" style="color: var(--flujo-primary-color);"></i>
                        Tips para registrar mejor tus materiales
                    </h3>
                    <ul class="landing-list" style="margin-top: 6px;">
                        <li>Indica el tipo de residuo de forma clara (plástico, cartón, vidrio, etc.).</li>
                        <li>Si no sabes el peso exacto, aproxima la cantidad en kilogramos.</li>
                        <li>Describe brevemente el estado de los materiales (limpios, separados, mezclados, etc.).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
