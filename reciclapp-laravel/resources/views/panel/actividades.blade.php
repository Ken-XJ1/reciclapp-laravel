@extends('layouts.app')

@section('title', 'Actividades y eventos')

@section('content')
    <div class="flujo-container" style="margin-top: 2rem; margin-bottom: 2rem;">
        <div style="margin-bottom: 12px;">
            <a href="{{ route('panel') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al panel</span>
            </a>
        </div>

        <div class="flujo-header" style="text-align: left; margin-bottom: 20px; border-bottom: none;">
            <div class="flujo-chip">
                <i class="fas fa-calendar-check"></i>
                <span>Actividades y eventos</span>
            </div>
            <h1>Mantente al dia con la comunidad</h1>
            <p>
                Aqui encontraras campañas, jornadas de recoleccion y eventos especiales organizados por ReciclApp
                y aliados en tu ciudad.
            </p>
        </div>

        <div class="landing-two-columns" style="align-items: flex-start;">
            <div>
                <div class="landing-card" style="margin-bottom: 14px;">
                    <h2 style="margin-top: 0; margin-bottom: 6px;">Proximas actividades</h2>
                    <p style="margin: 0 0 10px; font-size: 0.9rem; color: var(--flujo-text-light);">
                        Participar en estas actividades te permite sumar impacto, conocer a otras personas que reciclan
                        y aprender nuevas formas de cuidar el entorno.
                    </p>

                    <ul class="landing-list" style="margin-top: 8px;">
                        <li>
                            <span class="landing-list-icon" style="background-color: rgba(5,150,105,0.08);">
                                <i class="fas fa-users" style="color: var(--flujo-primary-color);"></i>
                            </span>
                            <div class="landing-list-text">
                                <h4>Jornadas comunitarias</h4>
                                <p>Eventos presenciales para recolectar residuos y sensibilizar sobre el reciclaje.</p>
                            </div>
                        </li>
                        <li>
                            <span class="landing-list-icon" style="background-color: rgba(5,150,105,0.08);">
                                <i class="fas fa-chalkboard-teacher" style="color: var(--flujo-primary-color);"></i>
                            </span>
                            <div class="landing-list-text">
                                <h4>Talleres y charlas</h4>
                                <p>Sesiones para aprender a separar residuos y aprovechar mejor los materiales.</p>
                            </div>
                        </li>
                        <li>
                            <span class="landing-list-icon" style="background-color: rgba(5,150,105,0.08);">
                                <i class="fas fa-gift" style="color: var(--flujo-primary-color);"></i>
                            </span>
                            <div class="landing-list-text">
                                <h4>Retos y campañas especiales</h4>
                                <p>Participa en campañas tematicas y gana puntos extra en tus entregas.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="landing-card" style="margin-bottom: 0;">
                    <h3 style="margin-top: 0;">Consejos para aprovechar los eventos</h3>
                    <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin-bottom: 6px;">
                        Revisa siempre la fecha, hora y lugar del evento. Llega con algunos minutos de anticipacion y
                        lleva tus residuos ya separados para facilitar la recoleccion.
                    </p>
                    <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin: 0;">
                        Despues del evento, revisa tu panel para ver si has sumado puntos o logros especiales.
                    </p>
                </div>
            </div>

            <div>
                @if($actividades->isEmpty())
                    <div class="landing-card" style="text-align: center;">
                        <h2 style="margin-top: 0; margin-bottom: 8px;">No hay actividades activas</h2>
                        <p style="margin: 0 0 6px; font-size: 0.95rem; color: var(--flujo-text-light);">
                            En este momento no hay eventos programados. Vuelve pronto, estamos preparando nuevas
                            iniciativas para la comunidad.
                        </p>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--flujo-text-light);">
                            Mientras tanto, puedes seguir reciclando desde tus puntos habituales y revisando tu
                            impacto en el panel principal.
                        </p>
                    </div>
                @else
                    <div class="landing-card" style="margin-bottom: 10px;">
                        <h2 style="margin-top: 0; margin-bottom: 4px;">Agenda de actividades</h2>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--flujo-text-light);">
                            Estos son los proximos eventos disponibles. Reserva la fecha y participa.
                        </p>
                    </div>

                    <div class="landing-grid" style="gap: 18px;">
                        @foreach($actividades as $actividad)
                            <div class="opcion-card user-card text-left">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; gap: 8px;">
                                        <h3 style="margin: 0; font-size: 1rem; color: var(--flujo-dark-color);">
                                            {{ $actividad->titulo }}
                                        </h3>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px;
                                            font-size: 11px; background-color: rgba(5,150,105,0.08); color: var(--flujo-primary-color);">
                                            <i class="fas fa-calendar-alt"></i>
                                            @if($actividad->fecha)
                                                {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m/Y H:i') }}
                                            @else
                                                Fecha por confirmar
                                            @endif
                                        </span>
                                    </div>

                                    <p style="margin: 0 0 4px; font-size: 0.85rem; color: var(--flujo-text-light);">
                                        @if($actividad->lugar)
                                            <i class="fas fa-map-marker-alt" style="margin-right: 4px; color: #f97373;"></i>
                                            {{ $actividad->lugar }}
                                        @else
                                            <span style="font-style: italic;">Lugar por confirmar</span>
                                        @endif
                                    </p>

                                    <p style="margin: 0; font-size: 0.9rem; color: var(--flujo-text-light);">
                                        {{ $actividad->descripcion }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
