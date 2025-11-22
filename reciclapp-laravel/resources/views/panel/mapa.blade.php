@extends('layouts.app')

@section('title', 'Puntos de reciclaje')

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
                <i class="fas fa-map-marker-alt"></i>
                <span>Puntos de reciclaje</span>
            </div>
            <h1>Encuentra puntos de reciclaje cerca de ti</h1>
            <p>
                Usa el mapa para ubicar puntos oficiales de reciclaje en Quibdo. El marcador del
                <strong>Parque Central</strong> te muestra un punto clave en el centro de la ciudad.
            </p>
        </div>

        <div class="landing-two-columns" style="align-items: flex-start;">
            <div>
                <div class="landing-card" style="margin-bottom: 14px;">
                    <h2 style="margin-top: 0; margin-bottom: 6px;">Resumen de puntos</h2>
                    <p style="margin: 0 0 10px; font-size: 0.9rem; color: var(--flujo-text-light);">
                        Estos son algunos puntos de reciclaje destacados. Seguiremos agregando mas lugares
                        a medida que la comunidad crece.
                    </p>

                    <ul class="landing-list" style="margin-top: 8px;">
                        <li>
                            <span class="landing-list-icon" style="background-color: rgba(5,150,105,0.08);">
                                <i class="fas fa-map-marker-alt" style="color: var(--flujo-primary-color);"></i>
                            </span>
                            <div class="landing-list-text">
                                <h4>Parque Central de Quibdo</h4>
                                <p>Punto fijo de reciclaje en el parque central de la ciudad.</p>
                                <p style="font-size: 0.85rem; color: var(--flujo-text-light); margin: 0;">
                                    Tipo: plastico, papel y vidrio
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="landing-card" style="margin-bottom: 0;">
                    <h3 style="margin-top: 0;">Como usar este mapa</h3>
                    <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin-bottom: 6px;">
                        Mueve el mapa, acerca o aleja con el zoom y pulsa sobre los marcadores para ver
                        detalles del punto de reciclaje.
                    </p>
                    <p style="font-size: 0.9rem; color: var(--flujo-text-light); margin: 0;">
                        Si conoces un nuevo lugar donde podria instalarse un punto, puedes usar la opcion
                        <strong>Proponer nuevo punto</strong> en tu panel.
                    </p>
                </div>
            </div>

            <div>
                <div class="landing-card" style="padding: 0; overflow: hidden;">
                    <div id="mapaDestinosFlujo" style="height: 420px; width: 100%; border-radius: 16px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        // Coordenadas aproximadas del centro de Quibdo (cerca del parque central)
        var centerLat = 5.6947;
        var centerLng = -76.6586;

        var map = L.map('mapaDestinosFlujo').setView([centerLat, centerLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'OpenStreetMap contributors'
        }).addTo(map);

        // Puntos de reciclaje destacados
        var puntos = [
            {
                nombre: 'Punto de reciclaje - Parque Central',
                descripcion: 'Punto fijo en el parque central de Quibdo.',
                lat: 5.6947,
                lng: -76.6586,
                tipo: 'Plastico, papel y vidrio'
            }
        ];

        puntos.forEach(function (p) {
            var marker = L.marker([p.lat, p.lng]).addTo(map);
            var popupHtml = '<strong>' + p.nombre + '</strong><br>' +
                '<span>' + p.descripcion + '</span><br>' +
                '<span style="font-size: 0.85rem; color: #047857;">Tipo: ' + p.tipo + '</span>';
            marker.bindPopup(popupHtml);
        });
    </script>
@endsection
