@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</h1>
    <p>Desde aquí podrás gestionar usuarios, propuestas de puntos, reportes y más.</p>

    <h3 class="mt-4">Estadísticas Rápidas</h3>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h4>Usuarios Activos</h4>
                <p>{{ $totalUsuarios }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h4>Puntos Activos</h4>
                <p>{{ $puntosActivos }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h4>Reportes Pendientes</h4>
                <p>{{ $reportesPendientes }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h4>Propuestas Pendientes</h4>
                <p>{{ $propuestasPendientes }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
