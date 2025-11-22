@extends('layouts.app')

@section('content')
<div class="flujo-container">
    <div style="margin-bottom: 16px;">
        <a href="{{ route('panel') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al panel</span>
        </a>
    </div>

    <div class="flujo-header" style="text-align: left;">
        <div class="flujo-chip">
            <i class="fas fa-recycle"></i>
            <span>Gestionar mi reciclaje</span>
        </div>
        <h1>Elige cómo quieres aportar hoy</h1>
        <p>Desde este espacio puedes proponer nuevos puntos, reportar problemas y registrar tus entregas para que tu impacto
            quede registrado y llegue al equipo administrador.</p>
    </div>

    <div class="lp-steps-layout" style="margin-top: 28px;">
        <!-- Columna izquierda: Acciones rápidas -->
        <div>
            <div class="landing-section-header" style="margin-bottom: 18px;">
                <span class="lp-section-kicker">Acciones rápidas</span>
                <h2 style="margin-bottom: 6px;">Gestiona tus acciones de reciclaje</h2>
                <p style="margin-bottom: 0; color: var(--flujo-text-light); font-size: 0.95rem;">
                    Elige una de las opciones para mantener actualizada la información de puntos y entregas de tu comunidad.
                </p>
            </div>

            <div class="lp-grid-4" style="gap: 16px;">
                <!-- Opción moderna 1: Proponer nuevo punto -->
                <div class="lp-feature-card">
                    <span class="lp-feature-icon">
                        <i class="fas fa-plus-circle"></i>
                    </span>
                    <h3>Proponer nuevo punto</h3>
                    <p>Comparte un lugar donde se pueda reciclar para que el equipo lo revise y pueda aparecer en el mapa.</p>
                    <a href="{{ route('panel.reciclaje.proponer') }}" class="btn-accion-flujo" style="margin-top: 14px;">
                        Proponer punto
                    </a>
                </div>

                <!-- Opción moderna 2: Reportar problema -->
                <div class="lp-feature-card">
                    <span class="lp-feature-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <h3>Reportar problema</h3>
                    <p>Avísanos si un punto está lleno, cerrado o presenta alguna novedad para que podamos gestionarlo.</p>
                    <a href="{{ route('panel.reciclaje.reportar') }}" class="btn-accion-flujo" style="margin-top: 14px;">
                        Reportar problema
                    </a>
                </div>

                <!-- Opción moderna 3: Registrar mi entrega -->
                <div class="lp-feature-card">
                    <span class="lp-feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <h3>Registrar mi entrega</h3>
                    <p>Registra los materiales que llevas a un punto de reciclaje para que tus aportes se sumen a tus puntos.</p>
                    <a href="{{ route('panel.reciclaje.registrar') }}" class="btn-accion-flujo" style="margin-top: 14px;">
                        Registrar entrega
                    </a>
                </div>
            </div>
        </div>

        <!-- Columna derecha: Guía y contexto -->
        <div>
            <div class="landing-card" style="margin-bottom: 18px;">
                <h3 style="margin-top: 0; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-lightbulb" style="color: var(--flujo-primary-color);"></i>
                    Guía rápida 
                </h3>
                <p style="font-size: 0.95rem; color: var(--flujo-text-light); margin-bottom: 12px;">
                    Sigue estos pasos para aprovechar al máximo tus acciones de reciclaje y que todo quede correctamente
                    registrado.
                </p>
                <ul class="lp-steps">
                    <li class="lp-step-item">
                        <div class="lp-step-index">1</div>
                        <div class="lp-step-content">
                            <h3>Ubica o propone un punto</h3>
                            <p>Si conoces un lugar nuevo para reciclar, proponlo. Si ya existe en el mapa, simplemente
                                utilízalo para tus entregas.</p>
                        </div>
                    </li>
                    <li class="lp-step-item">
                        <div class="lp-step-index">2</div>
                        <div class="lp-step-content">
                            <h3>Reporta novedades del punto</h3>
                            <p>Si encuentras un contenedor lleno, dañado o fuera de servicio, repórtalo para que el equipo
                                pueda actuar.</p>
                        </div>
                    </li>
                    <li class="lp-step-item">
                        <div class="lp-step-index">3</div>
                        <div class="lp-step-content">
                            <h3>Registra tus entregas</h3>
                            <p>Indica qué tipo de material entregaste y en qué cantidad. Tus aportes se enviarán al
                                administrador para validación.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="landing-card">
                <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shield-alt" style="color: var(--flujo-secondary-color);"></i>
                    ¿Qué hace el administrador con tu información?
                </h3>
                <p style="font-size: 0.95rem; color: var(--flujo-text-light); margin-bottom: 10px;">
                    Todo lo que registras en este panel se usa para mejorar la red de reciclaje y reconocer tu esfuerzo:
                </p>
                <ul class="landing-list">
                    <li>Las <strong>propuestas de puntos</strong> se revisan antes de publicarlas en el mapa público.</li>
                    <li>Los <strong>reportes de problemas</strong> ayudan a priorizar mantenimientos y correcciones.</li>
                    <li>Las <strong>entregas registradas</strong> se validan y se convierten en puntos para tu cuenta.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

