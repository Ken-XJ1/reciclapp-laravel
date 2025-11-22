@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel_flujo_style.css') }}">
@endsection

@section('content')
<div class="flujo-container">
    <div class="flujo-header">
        <h1>Proponer nuevo punto de reciclaje</h1>
        <p>Ayuda a otros usuarios compartiendo un lugar donde puedan llevar sus residuos reciclables.</p>
    </div>

    <div class="auth-card">
        <form action="{{ route('panel.reciclaje.guardar') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nombre">Nombre del punto</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ej: Centro de Reciclaje Barrio Verde" required>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo de reciclaje</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Selecciona el tipo</option>
                    <option value="Plástico">Plástico</option>
                    <option value="Papel">Papel</option>
                    <option value="Vidrio">Vidrio</option>
                    <option value="Orgánico">Orgánico</option>
                    <option value="Electrónico">Electrónico</option>
                    <option value="Mixto">Mixto</option>
                </select>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Calle, barrio, ciudad" required>
            </div>

            <div class="form-group">
                <label for="horario">Horario de atención</label>
                <input type="text" name="horario" id="horario" placeholder="Ej: Lunes a viernes 8:00-17:00" required>
            </div>

            <div class="form-group">
                <label for="contacto">Contacto (opcional)</label>
                <input type="text" name="contacto" id="contacto" placeholder="Teléfono, correo o redes">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción adicional (opcional)</label>
                <textarea name="descripcion" id="descripcion" rows="4" placeholder="Información extra útil"></textarea>
            </div>

            <button type="submit" class="auth-btn">Enviar propuesta</button>
        </form>
    </div>
</div>
@endsection
