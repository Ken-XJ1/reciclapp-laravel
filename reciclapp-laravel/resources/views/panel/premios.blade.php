@extends('layouts.app')

@section('title', 'Catálogo de Premios')

@section('content')
    <div class="flujo-container" style="margin-top: 2rem; margin-bottom: 2rem;">
        <style>
            .premio-filter-chip {
                border-radius: 999px;
                border: 1px solid var(--flujo-border-color);
                padding: 4px 10px;
                font-size: 0.8rem;
                background-color: #ffffff;
                color: var(--flujo-text-light);
                cursor: pointer;
                transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .premio-filter-chip:hover {
                background-color: rgba(5, 150, 105, 0.06);
            }

            .premio-filter-chip-active {
                background-color: var(--flujo-primary-color);
                color: #ffffff;
                border-color: transparent;
                box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            }
        </style>

        <div style="margin-bottom: 12px;">
            <a href="{{ route('panel') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al panel</span>
            </a>
        </div>

        <div class="flujo-header" style="text-align: left; margin-bottom: 20px; border-bottom: none;">
            <div class="flujo-chip">
                <i class="fas fa-gift"></i>
                <span>Canjear premios</span>
            </div>
            <h1>Convierte tus puntos en recompensas</h1>
            <p>Usa tus puntos acumulados para canjear vales, productos ecologicos o donaciones que reflejen tu impacto
                positivo en el reciclaje.</p>
        </div>

        @if(session('success'))
            <div class="alert-flujo alert-flujo-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert-flujo alert-flujo-error">{{ session('error') }}</div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 18px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 16px;">
                <div>
                    <h2 style="margin: 0 0 4px; font-size: 1.25rem; color: var(--flujo-dark-color);">Tus puntos disponibles</h2>
                    <p style="margin: 0; font-size: 0.9rem; color: var(--flujo-text-light);">
                        Cada entrega aprobada suma puntos a tu saldo. Cuando alcances los puntos requeridos, podras canjear
                        alguno de estos premios.
                    </p>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 10px; padding: 8px 14px; border-radius: 999px;
                    background-color: rgba(5,150,105,0.08); border: 1px solid var(--flujo-border-color);">
                    <i class="fas fa-star" style="color: var(--flujo-primary-color);"></i>
                    <span style="font-size: 0.9rem; color: var(--flujo-dark-color);">
                        <strong>Puntos actuales:</strong>
                        <span id="user-points" data-points="{{ auth()->user()->puntos_acumulados ?? 0 }}" style="color: var(--flujo-primary-color);">
                            {{ auth()->user()->puntos_acumulados ?? 0 }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="landing-card" style="margin-bottom: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div>
                        <h2 style="margin: 0 0 4px; font-size: 1.2rem;">Catalogo de premios</h2>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--flujo-text-light);">
                            Filtra por tipo de recompensa y elige como quieres usar tus puntos.
                        </p>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <button type="button" class="premio-filter-chip premio-filter-chip-active" data-filter="all">
                            Todos
                        </button>
                        <button type="button" class="premio-filter-chip" data-filter="vouchers">
                            Cupones
                        </button>
                        <button type="button" class="premio-filter-chip" data-filter="eco">
                            Eco productos
                        </button>
                        <button type="button" class="premio-filter-chip" data-filter="donations">
                            Donaciones
                        </button>
                        <button type="button" class="premio-filter-chip" data-filter="other">
                            Otros
                        </button>
                    </div>
                </div>
            </div>

            @if($premios->isEmpty())
                <div class="landing-card">
                    <p style="margin: 0; font-size: 0.95rem; color: var(--flujo-text-light);">
                        No hay premios disponibles actualmente. Vuelve mas tarde o revisa tus puntos en el panel.
                    </p>
                </div>
            @else
                <div class="landing-grid" style="gap: 18px;">
                    @php
                        $counts = [
                            'vouchers' => 0,
                            'eco' => 0,
                            'donations' => 0,
                            'other' => 0,
                        ];
                    @endphp

                    @foreach($premios as $premio)
                        @php
                            $nombrePremio = strtolower($premio->nombre ?? $premio->nombre_premio ?? '');
                            $rawCategoria = strtolower($premio->categoria ?? '');

                            if ($rawCategoria === '') {
                                if (strpos($nombrePremio, 'vale') !== false || strpos($nombrePremio, 'voucher') !== false || strpos($nombrePremio, 'cupon') !== false) {
                                    $rawCategoria = 'vouchers';
                                } elseif (strpos($nombrePremio, 'arbol') !== false || strpos($nombrePremio, 'arboles') !== false || strpos($nombrePremio, 'donacion') !== false) {
                                    $rawCategoria = 'donations';
                                } elseif (strpos($nombrePremio, 'taza') !== false || strpos($nombrePremio, 'reutilizable') !== false || strpos($nombrePremio, 'eco') !== false || strpos($nombrePremio, 'botella') !== false) {
                                    $rawCategoria = 'eco';
                                } else {
                                    $rawCategoria = 'other';
                                }
                            }

                            if (!isset($counts[$rawCategoria])) {
                                $counts[$rawCategoria] = 0;
                            }
                            $counts[$rawCategoria]++;

                            if ($rawCategoria === 'vouchers') {
                                $categoriaLabel = 'Cupones';
                            } elseif ($rawCategoria === 'donations') {
                                $categoriaLabel = 'Donaciones';
                            } elseif ($rawCategoria === 'eco') {
                                $categoriaLabel = 'Eco productos';
                            } else {
                                $categoriaLabel = 'Premios';
                            }

                            $imageUrl = null;
                            if (!empty($premio->imagen)) {
                                $imageUrl = asset($premio->imagen);
                            } else {
                                if ($rawCategoria === 'vouchers') {
                                    $imageUrl = 'https://images.pexels.com/photos/1341304/pexels-photo-1341304.jpeg?auto=compress&cs=tinysrgb&w=800';
                                } elseif ($rawCategoria === 'eco') {
                                    $imageUrl = 'https://images.pexels.com/photos/3735210/pexels-photo-3735210.jpeg?auto=compress&cs=tinysrgb&w=800';
                                } elseif ($rawCategoria === 'donations') {
                                    $imageUrl = 'https://images.pexels.com/photos/1214259/pexels-photo-1214259.jpeg?auto=compress&cs=tinysrgb&w=800';
                                } else {
                                    $imageUrl = asset('img/default.png');
                                }
                            }
                        @endphp

                        <div class="opcion-card user-card text-left premio-card" data-category="{{ $rawCategoria }}">
                            <div>
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $premio->nombre ?? $premio->nombre_premio }}"
                                     style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px; display: block; margin-bottom: 12px;">

                                <h3 class="text-lg font-semibold mb-1">{{ $premio->nombre ?? $premio->nombre_premio }}</h3>
                                <p class="text-sm mb-2">{{ $premio->descripcion ?? $premio->descripcion_premio }}</p>

                                <p style="margin: 0 0 6px;">
                                    <span style="display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 11px;
                                        background-color: rgba(5,150,105,0.06); color: var(--flujo-primary-color);">
                                        {{ $categoriaLabel }}
                                    </span>
                                </p>

                                <p class="text-sm" style="margin-bottom: 4px; font-weight: bold; color: var(--flujo-primary-color); font-size: 0.95rem;">
                                    Puntos requeridos: {{ $premio->puntos_necesarios ?? $premio->puntos_requeridos }}
                                </p>
                            </div>

                            <form action="{{ route('panel.premios.canjear', $premio->_id ?? $premio->id_premio) }}" method="POST" style="margin-top: 14px;">
                                @csrf
                                <button type="submit" class="btn-accion-flujo" style="width: 100%; text-align: center;">
                                    Canjear premio
                                </button>
                            </form>
                        </div>
                    @endforeach

                    @php
                        $placeholders = [
                            'vouchers' => [
                                [
                                    'titulo' => 'Cupon de 10.000 en Fuller',
                                    'descripcion' => 'Cupon de 10.000 pesos para compras en Fuller Quibdo.',
                                    'puntos' => 500,
                                ],
                                [
                                    'titulo' => 'Cupon de 20.000 en Alkosto',
                                    'descripcion' => 'Cupon de 20.000 pesos para usar en Alkosto Quibdo.',
                                    'puntos' => 800,
                                ],
                                [
                                    'titulo' => 'Cupon de 15.000 en Confimax',
                                    'descripcion' => 'Cupon de 15.000 pesos para compras en Confimax Quibdo.',
                                    'puntos' => 600,
                                ],
                            ],
                            'eco' => [
                                [
                                    'titulo' => 'Vaso reutilizable',
                                    'descripcion' => 'Vaso de material reutilizable para reducir residuos.',
                                    'puntos' => 700,
                                ],
                                [
                                    'titulo' => 'Botella de agua reutilizable',
                                    'descripcion' => 'Botella para usar todos los dias y evitar plastico de un solo uso.',
                                    'puntos' => 900,
                                ],
                                [
                                    'titulo' => 'Kit eco para el hogar',
                                    'descripcion' => 'Pack con productos ecologicos para tu casa.',
                                    'puntos' => 1200,
                                ],
                            ],
                            'donations' => [
                                [
                                    'titulo' => 'Plantar un arbol',
                                    'descripcion' => 'Donacion para apoyar la siembra de un arbol.',
                                    'puntos' => 1000,
                                ],
                                [
                                    'titulo' => 'Apoyo a limpieza de rios',
                                    'descripcion' => 'Tu donacion ayuda a jornadas de limpieza.',
                                    'puntos' => 1500,
                                ],
                                [
                                    'titulo' => 'Fondo para educacion ambiental',
                                    'descripcion' => 'Apoya talleres de reciclaje en escuelas.',
                                    'puntos' => 1300,
                                ],
                            ],
                            'other' => [
                                [
                                    'titulo' => 'Membresia Reciclapp',
                                    'descripcion' => 'Acceso a beneficios y retos especiales.',
                                    'puntos' => 1100,
                                ],
                                [
                                    'titulo' => 'Entrada a evento verde',
                                    'descripcion' => 'Pase para actividades sobre sostenibilidad.',
                                    'puntos' => 900,
                                ],
                                [
                                    'titulo' => 'Acceso a contenido premium',
                                    'descripcion' => 'Cursos y material digital sobre reciclaje.',
                                    'puntos' => 700,
                                ],
                            ],
                        ];
                    @endphp

                    @foreach($placeholders as $catKey => $items)
                        @php
                            $actual = $counts[$catKey] ?? 0;
                        @endphp
                        @for($i = $actual; $i < 3 && $i < count($items); $i++)
                            @php $demo = $items[$i]; @endphp
                            <div class="opcion-card user-card text-left premio-card" data-category="{{ $catKey }}">
                                <div>
                                    <div style="width: 100%; height: 180px; border-radius: 10px; background: linear-gradient(135deg, #059669, #10b981); margin-bottom: 12px;"></div>

                                    <h3 class="text-lg font-semibold mb-1">{{ $demo['titulo'] }}</h3>
                                    <p class="text-sm mb-2">{{ $demo['descripcion'] }}</p>

                                    <p style="margin: 0 0 6px;">
                                        <span style="display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 11px;
                                            background-color: rgba(5,150,105,0.06); color: var(--flujo-primary-color);">
                                            Ejemplo {{ $catKey === 'vouchers' ? 'Cupones' : ($catKey === 'eco' ? 'Eco productos' : ($catKey === 'donations' ? 'Donaciones' : 'Premios')) }}
                                        </span>
                                    </p>

                                    <p class="text-sm" style="margin-bottom: 4px; font-weight: bold; color: var(--flujo-primary-color); font-size: 0.95rem;">
                                        Puntos requeridos: {{ $demo['puntos'] }}
                                    </p>
                                </div>

                                <button type="button" class="btn-accion-flujo premio-demo-btn" data-required="{{ $demo['puntos'] }}" style="width: 100%; text-align: center;">
                                    Canjear
                                </button>
                            </div>
                        @endfor
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var chips = document.querySelectorAll('.premio-filter-chip');
            var cards = document.querySelectorAll('.premio-card');

            chips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    var filter = chip.getAttribute('data-filter');

                    chips.forEach(function (c) {
                        c.classList.remove('premio-filter-chip-active');
                    });
                    chip.classList.add('premio-filter-chip-active');

                    cards.forEach(function (card) {
                        var cat = card.getAttribute('data-category') || 'other';
                        if (filter === 'all' || filter === cat) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            var userPointsElement = document.getElementById('user-points');
            var userPoints = 0;
            if (userPointsElement) {
                var raw = userPointsElement.getAttribute('data-points') || '0';
                userPoints = parseInt(raw, 10) || 0;
            }

            var demoButtons = document.querySelectorAll('.premio-demo-btn');
            demoButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var requiredRaw = btn.getAttribute('data-required') || '0';
                    var required = parseInt(requiredRaw, 10) || 0;

                    if (userPoints < required) {
                        alert('Puntos insuficientes');
                    } else {
                        alert('Canje registrado correctamente (ejemplo).');
                    }
                });
            });
        });
    </script>
@endsection

