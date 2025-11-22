<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex">

    <div class="sidebar w-64 bg-slate-900/80 border-r border-slate-800 backdrop-blur flex flex-col max-h-screen overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-800">
            <h2 class="text-xl font-semibold tracking-tight flex items-center gap-2">
                <i class="fas fa-recycle text-emerald-400"></i>
                <span>ReciclApp Admin</span>
            </h2>
        </div>
        <ul class="flex-1 px-3 py-4 space-y-1 text-sm">
            <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-home w-4"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('admin.usuarios') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-users w-4"></i> <span>Usuarios</span></a></li>
            <li><a href="{{ route('admin.premios') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-gift w-4"></i> <span>Premios</span></a></li>
            <li><a href="{{ route('admin.propuestas') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-map-marker-alt w-4"></i> <span>Propuestas</span></a></li>
            <li><a href="{{ route('admin.reportes') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-flag w-4"></i> <span>Reportes</span></a></li>
            <li><a href="{{ route('admin.entregas') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-truck-loading w-4"></i> <span>Entregas</span></a></li>
            <li><a href="{{ route('admin.auditoria') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-clipboard-list w-4"></i> <span>Auditoría</span></a></li>
            <li><a href="{{ route('admin.actividades') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-calendar-check w-4"></i> <span>Actividades</span></a></li>
            <li><a href="{{ route('admin.logros') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition"><i class="fas fa-trophy w-4"></i> <span>Logros</span></a></li>
        </ul>
        <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-400">
            <a href="{{ route('logout') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 transition">
                <i class="fas fa-sign-out-alt w-4"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content flex-1 px-6 py-6 overflow-y-auto">
        @yield('content')
    </div>

</body>
</html>
