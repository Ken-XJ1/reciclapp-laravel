@extends('layouts.app')

@section('title', $title ?? 'Panel de Usuario')

@section('content')
@php
    $active = $active ?? 'resumen';
@endphp
<div class="user-shell">
    {{-- Sidebar izquierda reutilizable --}}
    <aside class="user-sidebar">
        <div class="user-sidebar-header">
            <div class="user-sidebar-logo-circle">
                <i class="fas fa-recycle"></i>
            </div>
            <span class="user-sidebar-title">ReciclApp</span>
        </div>

        <nav class="user-sidebar-nav">
            <a href="{{ route('panel') }}" class="user-nav-link {{ $active === 'resumen' ? 'user-nav-link-active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Resumen</span>
            </a>
            <a href="{{ route('panel.reciclaje') }}" class="user-nav-link {{ $active === 'reciclaje' ? 'user-nav-link-active' : '' }}">
                <i class="fas fa-recycle"></i>
                <span>Gestionar reciclaje</span>
            </a>
            <a href="{{ route('panel.premios') }}" class="user-nav-link {{ $active === 'premios' ? 'user-nav-link-active' : '' }}">
                <i class="fas fa-gift"></i>
                <span>Canjear premios</span>
            </a>
            <a href="{{ route('panel.mapa') }}" class="user-nav-link {{ $active === 'mapa' ? 'user-nav-link-active' : '' }}">
                <i class="fas fa-map-marked-alt"></i>
                <span>Puntos de reciclaje</span>
            </a>
            <a href="{{ route('panel.actividades') }}" class="user-nav-link {{ $active === 'actividades' ? 'user-nav-link-active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Actividades y eventos</span>
            </a>
        </nav>

        <div class="user-sidebar-user">
            <div class="user-avatar-circle">
                {{ strtoupper(mb_substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
            </div>
            <div class="user-sidebar-user-info">
                <div class="user-sidebar-name">{{ auth()->user()->nombre ?? 'Usuario' }}</div>
                <div class="user-sidebar-email">{{ auth()->user()->email ?? '' }}</div>
            </div>
        </div>
    </aside>

    {{-- Contenido principal específico de cada pantalla --}}
    <section class="user-main">
        @yield('user_main')
    </section>
</div>
@endsection
