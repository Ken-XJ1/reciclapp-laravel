@extends('layouts.app')

@section('content')
<div class="flujo-container" style="margin-top: 2rem; margin-bottom: 2rem;">
    @if(session('success'))
        <div class="alert-flujo alert-flujo-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert-flujo alert-flujo-error">{{ session('error') }}</div>
    @endif
    <div class="flujo-header" style="margin-bottom: 24px; text-align: left;">
        <div class="flujo-chip">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Reportar problema en un punto</span>
        </div>
        <h1>Ayúdanos a mantener la red de reciclaje en buen estado</h1>
        <p>Selecciona el punto y describe lo que sucede para que podamos tomar acciones y que otros usuarios tengan
            información confiable.</p>
    </div>

    <div class="landing-two-columns" style="margin-top: 8px;">
        <div>
            <form action="{{ route('panel.reciclaje.reportar.guardar') }}" method="POST" class="form-section">
                @csrf

                <div class="form-group">
                    <label for="punto_id">Punto de reciclaje</label>
                    <select name="punto_id" id="punto_id">
                        <option value="">Selecciona un punto</option>
                        @foreach($puntos as $punto)
                            <option value="{{ $punto->_id ?? $punto->id }}">{{ $punto->nombre ?? 'Punto sin nombre' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_problema">Tipo de problema</label>
                    <select name="tipo_problema" id="tipo_problema" onchange="mostrarOtro(this.value)">
                        <option value="direccion_incorrecta">Dirección incorrecta</option>
                        <option value="zona_peligrosa">Zona peligrosa</option>
                        <option value="punto_inaccesible">Punto inaccesible</option>
                        <option value="limpieza_inadecuada">Limpieza inadecuada</option>
                        <option value="horario_incorrecto">Horario incorrecto</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="form-group" id="otros_detalle" style="display:none;">
                    <label for="descripcion_otro">Especifica el problema</label>
                    <textarea name="descripcion_otro" id="descripcion_otro" rows="4" placeholder="Describe aquí el problema..."></textarea>
                </div>

                <div class="form-group">
                    <label for="descripcion">Comentarios adicionales</label>
                    <textarea name="descripcion" id="descripcion" rows="4" placeholder="Detalles adicionales, si los hay..."></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; align-items: center;">
                    <button type="submit" class="btn-accion-flujo">Enviar reporte</button>
                    <a href="{{ route('panel.reciclaje') }}" class="btn-volver">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver</span>
                    </a>
                </div>
            </form>
        </div>

        <div>
            <div class="landing-card" style="margin-bottom: 18px;">
                <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-circle-exclamation" style="color: var(--flujo-secondary-color);"></i>
                    ¿Por qué es importante reportar?
                </h3>
                <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin-bottom: 10px;">
                    Cada reporte ayuda a detectar puntos con problemas antes de que afecten a más personas. Así
                    podemos priorizar limpiezas, cambios de ubicación o correcciones en la información.
                </p>

                <div style="margin-top: 10px;">
                    <div style="display: flex; align-items: flex-end; gap: 12px; height: 110px;">
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #bbf7d0; height: 35px;"></div>
                            <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">3 pts sin reportes</span>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #34d399; height: 70px;"></div>
                            <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">7 pts con reportes</span>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #059669; height: 95px;"></div>
                            <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">12 pts atendidos</span>
                        </div>
                    </div>
                    <p style="margin-top: 8px; font-size: 11px; color: #6b7280;">
                        En este periodo, 7 puntos reportados permitieron que 12 ubicaciones fueran atendidas y mejoradas
                        frente a solo 3 sin incidentes registrados.
                    </p>
                </div>
            </div>

            <div class="landing-card">
                <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-bullhorn" style="color: var(--flujo-primary-color);"></i>
                    Tips para un buen reporte
                </h3>
                <ul class="landing-list" style="margin-top: 6px;">
                    <li>Indica si el punto está inaccesible, sucio, dañado o si la dirección parece incorrecta.</li>
                    <li>Si es seguro, puedes mencionar referencias cercanas (parques, esquinas, locales).</li>
                    <li>Usa el campo "Otros" solo cuando el problema no encaje en las categorías anteriores.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function mostrarOtro(valor) {
        const otros = document.getElementById('otros_detalle');
        otros.style.display = valor === 'otros' ? 'block' : 'none';
    }
</script>
@endsection
