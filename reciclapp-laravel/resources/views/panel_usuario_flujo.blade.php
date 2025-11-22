@extends('layouts.user_panel')

@section('user_main')
        <header class="user-main-header">
            <div>
                <h1>Gestionar reciclaje</h1>
                <p>Revisa tu progreso y accede rápidamente a las principales acciones de la plataforma.</p>
            </div>
            <a href="{{ route('panel.reciclaje.registrar') }}" class="user-main-cta">
                <i class="fas fa-plus"></i>
                <span>Registrar nueva entrega</span>
            </a>
        </header>

        <div class="user-stats-grid">
            <div class="user-stat-card">
                <div class="user-stat-icon user-stat-icon-primary">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="user-stat-content">
                    <span class="user-stat-label">Puntos acumulados</span>
                    <span class="user-stat-value">{{ $puntos ?? 0 }}</span>
                </div>
            </div>
            <div class="user-stat-card">
                <div class="user-stat-icon user-stat-icon-teal">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="user-stat-content">
                    <span class="user-stat-label">Entregas aprobadas</span>
                    <span class="user-stat-value">{{ $entregasAprobadas ?? 0 }}</span>
                </div>
            </div>
            <div class="user-stat-card">
                <div class="user-stat-icon user-stat-icon-gold">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="user-stat-content">
                    <span class="user-stat-label">Canjes realizados</span>
                    <span class="user-stat-value">{{ $canjes ?? 0 }}</span>
                </div>
            </div>
        </div>

        <section class="user-section">
            <div class="user-section-header">
                <div>
                    <h2>Impacto de tu reciclaje</h2>
                    <p>Una vista rápida de cómo tus acciones podrían reflejarse en el impacto ambiental.</p>
                </div>
            </div>
            <div class="user-section-body">
                <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                    <div style="padding: 12px; border-radius: 0.9rem; background-color: #ffffff; border: 1px solid #e5e7eb; box-shadow: 0 4px 8px rgba(15,23,42,0.08);">
                        <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 8px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-chart-column" style="color: #059669;"></i>
                            <span>Kg reciclados por mes</span>
                        </h3>
                        <div style="display: flex; align-items: flex-end; gap: 12px; height: 120px; margin-top: 4px;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #bbf7d0; height: 40px;"></div>
                                <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">Ene<br>12 kg</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #6ee7b7; height: 65px;"></div>
                                <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">Feb<br>19 kg</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #34d399; height: 80px;"></div>
                                <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">Mar<br>24 kg</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 14px; border-radius: 999px 999px 0 0; background-color: #059669; height: 95px;"></div>
                                <span style="margin-top: 4px; font-size: 11px; color: #6b7280; text-align: center;">Abr<br>29 kg</span>
                            </div>
                        </div>
                        <p style="margin-top: 8px; font-size: 11px; color: #6b7280;">
                            Estas cantidades son una referencia para visualizar cómo podrían crecer tus aportes mes a mes
                            a medida que registres más entregas.
                        </p>
                    </div>

                    <div style="padding: 12px; border-radius: 0.9rem; background-color: #ffffff; border: 1px solid #e5e7eb; box-shadow: 0 4px 8px rgba(15,23,42,0.08);">
                        <h3 style="font-size: 0.9rem; font-weight: 600; margin: 0 0 8px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-earth-americas" style="color: #0d9488;"></i>
                            <span>Beneficios estimados</span>
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: flex-start; gap: 6px; font-size: 13px; color: #4b5563; margin-bottom: 6px;">
                                <span style="margin-top: 4px; width: 8px; height: 8px; border-radius: 999px; background-color: #10b981;"></span>
                                <span>Cada 10 kg de plástico reciclado pueden evitar la emisión de varios kg de CO₂.</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 6px; font-size: 13px; color: #4b5563; margin-bottom: 6px;">
                                <span style="margin-top: 4px; width: 8px; height: 8px; border-radius: 999px; background-color: #14b8a6;"></span>
                                <span>Tus entregas aprobadas ayudan a mantener más limpios los puntos de reciclaje.</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 6px; font-size: 13px; color: #4b5563;">
                                <span style="margin-top: 4px; width: 8px; height: 8px; border-radius: 999px; background-color: #fbbf24;"></span>
                                <span>Estos datos permiten planear campañas y mejoras en tu comunidad.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
@endsection
