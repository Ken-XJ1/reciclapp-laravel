@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel_flujo_style.css') }}">
@endsection

@section('content')
<div class="flujo-container">
    <div class="flujo-header">
        <h1>Reportar un problema en un punto de reciclaje</h1>
        <p>Ayuda a mejorar la red de reciclaje informando sobre problemas en los puntos existentes.</p>
    </div>

    <div class="auth-card">
        <form action="{{ route('panel.reciclaje.guardar') }}" method="POST">
            @csrf

            
            <div class="form-group">
                <label for="punto">Selecciona el punto de reciclaje</label>
                <select name="punto" id="punto" required>
                    <option value="">-- Elige un punto --</option>
                    {{-- Aquí deberías cargar los puntos existentes desde la base de datos --}}
                    @foreach($puntos as $punto)
                        <option value="{{ $punto->id }}">{{ $punto->nombre }} - {{ $punto->direccion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tipo_problema">Tipo de problema</label>
                <select name="tipo_problema" id="tipo_problema" required>
                    <option value="">-- Selecciona un tipo --</option>
                    <option value="Direccion incorrecta">Dirección incorrecta</option>
                    <option value="Zona peligrosa">Zona peligrosa</option>
                    <option value="Punto cerrado">Punto cerrado</option>
                    <option value="Material no permitido">Material no permitido</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

        
            <div class="form-group">
                <label for="descripcion">Descripción del problema</label>
                <textarea name="descripcion" id="descripcion" rows="4" placeholder="Especifica el problema con detalle, por ejemplo: la calle está en mal estado, hay desechos mezclados, etc." required></textarea>
            </div>

            <button type="submit" class="auth-btn">Enviar reporte</button>
        </form>
    </div>
</div>
@endsection
