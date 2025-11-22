@extends('layouts.app')

@section('content')

<section class="lp-hero" id="inicio">
    <div class="lp-hero-inner">
        <div class="lp-hero-text">
            <span class="lp-hero-tag">
                <i class="fas fa-leaf"></i>
                Reciclaje inteligente para tu ciudad
            </span>
            <h1>Convierte cada residuo en una oportunidad para el planeta.</h1>
            <p>
                ReciclApp te ayuda a encontrar puntos de reciclaje, registrar tus entregas y visualizar el impacto real de tus acciones de una forma sencilla y motivadora.
            </p>
            <div class="lp-hero-actions">
                <a href="{{ route('register') }}" class="lp-btn lp-btn-primary">
                    Comenzar ahora
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('login') }}" class="lp-btn lp-btn-ghost">
                    <i class="fas fa-user"></i>
                    Ya tengo cuenta
                </a>
            </div>
        </div>

        <div class="lp-hero-media">
            <div class="lp-hero-forest">
                <div class="lp-hero-forest-bg-main"></div>
                <div class="lp-hero-forest-bg-leaf lp-hero-forest-bg-leaf-left"></div>
                <div class="lp-hero-forest-bg-leaf lp-hero-forest-bg-leaf-right"></div>

                <div class="lp-hero-card">
                    <div class="lp-hero-card-header">
                        <span class="lp-hero-card-title">Tu impacto hoy</span>
                        <span class="lp-hero-card-pill">
                            <i class="fas fa-seedling"></i>
                            En crecimiento
                        </span>
                    </div>
                    <div class="lp-hero-card-body">
                        <div class="lp-tip-slide active">
                            <p class="lp-hero-card-copy">
                                Separar plástico, papel, vidrio y orgánicos desde casa facilita que más material pueda reciclarse correctamente.
                            </p>
                        </div>
                        <div class="lp-tip-slide">
                            <p class="lp-hero-card-copy">
                                Enjuaga rápidamente los envases antes de llevarlos al punto de reciclaje para evitar malos olores y contaminación de otros residuos.
                            </p>
                        </div>
                        <div class="lp-tip-slide">
                            <p class="lp-hero-card-copy">
                                El mejor residuo es el que no se genera: reutiliza bolsas, botellas y frascos siempre que sea posible.
                            </p>
                        </div>
                        <div class="lp-tip-dots">
                            <span class="lp-tip-dot active"></span>
                            <span class="lp-tip-dot"></span>
                            <span class="lp-tip-dot"></span>
                        </div>
                    </div>
                    <div class="lp-hero-card-footer">
                        <span class="lp-hero-card-footer-text">
                            Cada registro cuenta para acercarnos a una ciudad más limpia.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="about" class="lp-section lp-section-light">
    <div class="lp-section-inner">
        <div class="lp-about-layout">
            <div class="lp-section-header">
            <span class="lp-section-kicker">¿Por qué ReciclApp?</span>
            <h2>Una plataforma pensada para personas, comunidades y ciudades.</h2>
            <p>
                ReciclApp conecta a ciudadanos, puntos de reciclaje y administradores en un mismo lugar para que el proceso de reciclar sea claro, trazable y motivador.
            </p>
            </div>

            <div class="lp-about-carousel">
                <div class="welcome-visual welcome-visual-main">
                    <div class="welcome-slide active">
                        <img src="{{ asset('media/reci.jpg') }}" alt="Personas reciclando en un punto limpio">
                    </div>
                    <div class="welcome-slide">
                        <img src="{{ asset('media/reci2.jpg') }}" alt="Clasificacion de residuos para reciclaje">
                    </div>
                    <div class="welcome-slide">
                        <img src="{{ asset('media/reci3.jpg') }}" alt="Materiales listos para ser reciclados">
                    </div>
                    <div class="welcome-slide-indicators">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lp-grid-4">
            <article class="lp-feature-card">
                <i class="fas fa-location-dot lp-feature-icon"></i>
                <h3>Encuentra dónde reciclar</h3>
                <p>Explora un mapa de puntos de reciclaje verificados y descubre qué materiales acepta cada uno.</p>
            </article>
            <article class="lp-feature-card">
                <i class="fas fa-recycle lp-feature-icon"></i>
                <h3>Registra tus entregas</h3>
                <p>Lleva el historial de tus entregas y consulta cuánto has reciclado en cualquier momento.</p>
            </article>
            <article class="lp-feature-card">
                <i class="fas fa-medal lp-feature-icon"></i>
                <h3>Gana puntos y logros</h3>
                <p>Obtén puntos, logros y recompensas por mantener el hábito de reciclar de forma constante.</p>
            </article>
            <article class="lp-feature-card">
                <i class="fas fa-chart-line lp-feature-icon"></i>
                <h3>Mide tu impacto</h3>
                <p>Visualiza estadísticas sobre tu contribución ambiental y compártelas con tu comunidad.</p>
            </article>
        </div>
    </div>
</section>

<section id="features" class="lp-section lp-section-muted">
    <div class="lp-section-inner lp-steps-layout">
        <div class="lp-section-header">
            <span class="lp-section-kicker">¿Cómo funciona?</span>
            <h2>Empieza a reciclar con ReciclApp en solo tres pasos.</h2>
        </div>
        <ol class="lp-steps">
            <li class="lp-step-item">
                <div class="lp-step-index">1</div>
                <div class="lp-step-content">
                    <h3>Crea tu cuenta y completa tu perfil</h3>
                    <p>Regístrate en minutos, indica tu ciudad y empiezas a ver los puntos de reciclaje cercanos.</p>
                </div>
            </li>
            <li class="lp-step-item">
                <div class="lp-step-index">2</div>
                <div class="lp-step-content">
                    <h3>Encuentra un punto y registra tu entrega</h3>
                    <p>Entrega tus materiales en un punto aliado, registra lo que reciclaste y genera puntos.</p>
                </div>
            </li>
            <li class="lp-step-item">
                <div class="lp-step-index">3</div>
                <div class="lp-step-content">
                    <h3>Sigue tu progreso y canjea recompensas</h3>
                    <p>Consulta tu impacto acumulado y canjea beneficios definidos por tu institución o comunidad.</p>
                </div>
            </li>
        </ol>
    </div>
</section>

<section class="lp-section lp-section-cta">
    <div class="lp-section-inner lp-cta-inner">
        <div>
            <h2>¿Listo para llevar tu reciclaje al siguiente nivel?</h2>
            <p>Regístrate gratis, empieza a registrar tus acciones y muestra el impacto real que generas.</p>
        </div>
        <div class="lp-cta-actions">
            <a href="{{ route('register') }}" class="lp-btn lp-btn-primary">
                Crear mi cuenta
                <i class="fas fa-arrow-right"></i>
            </a>
            <a href="{{ route('login') }}" class="lp-btn lp-btn-outline">
                Ya tengo cuenta
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Carruseles de imagenes (pueden existir varios .welcome-visual)
        const carousels = document.querySelectorAll('.welcome-visual');

        carousels.forEach(function (carousel) {
            const slides = carousel.querySelectorAll('.welcome-slide');
            const dots = carousel.querySelectorAll('.welcome-slide-indicators .dot');
            let current = 0;

            if (slides.length === 0) {
                return;
            }

            function showSlide(index) {
                slides[current].classList.remove('active');
                if (dots[current]) {
                    dots[current].classList.remove('active');
                }
                current = index;
                slides[current].classList.add('active');
                if (dots[current]) {
                    dots[current].classList.add('active');
                }
            }

            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    showSlide(index);
                });
            });

            setInterval(function () {
                const next = (current + 1) % slides.length;
                showSlide(next);
            }, 9000);
        });

        // Carrusel de tips en la tarjeta "Tu impacto hoy"
        const tipSlides = document.querySelectorAll('.lp-tip-slide');
        const tipDots = document.querySelectorAll('.lp-tip-dots .lp-tip-dot');
        let tipCurrent = 0;

        if (tipSlides.length > 0) {
            function showTip(index) {
                tipSlides[tipCurrent].classList.remove('active');
                tipDots[tipCurrent].classList.remove('active');
                tipCurrent = index;
                tipSlides[tipCurrent].classList.add('active');
                tipDots[tipCurrent].classList.add('active');
            }

            tipDots.forEach((dot, index) => {
                dot.addEventListener('click', () => showTip(index));
            });

            setInterval(function () {
                const nextTip = (tipCurrent + 1) % tipSlides.length;
                showTip(nextTip);
            }, 7000);
        }
    });
</script>
@endpush
