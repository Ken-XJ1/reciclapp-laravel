<?php
// admin.php
$titulo_pagina = "Dashboard Principal";

include 'admin_layout.php'; 
include 'conexion.php'; 

// Inicializar estadísticas
$total_usuarios = 0;
$puntos_activos = 0;
$reportes_pendientes_recoleccion = 0;
$propuestas_puntos_pendientes = 0;

// Si hay conexión, hacer consultas
if (isset($conn) && $conn && !$conn->connect_error) {
    // Usuarios activos
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE estado = 'activo'");
    if ($stmt) {
        $row = $stmt->fetch_assoc();
        $total_usuarios = $row['total'];
    }

    // Puntos activos
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM puntos_de_reciclaje WHERE estado_punto = 'activo'");
    if ($stmt) {
        $row = $stmt->fetch_assoc();
        $puntos_activos = $row['total'];
    }

    // Reportes pendientes
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM reportes_recoleccion_usuario WHERE estado_validacion_admin = 'pendiente'");
    if ($stmt) {
        $row = $stmt->fetch_assoc();
        $reportes_pendientes_recoleccion = $row['total'];
    }

    // Propuestas pendientes
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM puntos_de_reciclaje WHERE estado_punto = 'pendiente_aprobacion'");
    if ($stmt) {
        $row = $stmt->fetch_assoc();
        $propuestas_puntos_pendientes = $row['total'];
    }
}
?>

<div class="dashboard">
    <h2><i class="fas fa-tachometer-alt"></i> Bienvenido al Panel de Administración</h2>
    <p>Desde aquí podrás gestionar usuarios, propuestas, reportes y más.</p>

    <h3> Estadísticas Rápidas</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h4>Usuarios Activos</h4>
            <p><?php echo $total_usuarios; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-recycle"></i>
            <h4>Puntos Activos</h4>
            <p><?php echo $puntos_activos; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-truck"></i>
            <h4>Reportes Pendientes</h4>
            <p><?php echo $reportes_pendientes_recoleccion; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-map-marker-alt"></i>
            <h4>Propuestas Pendientes</h4>
            <p><?php echo $propuestas_puntos_pendientes; ?></p>
        </div>
    </div>
</div>

<?php
if (isset($conn) && $conn && $conn->ping()) {
    $conn->close();
}
?>

</div> <!-- cierre de main-content -->
</body>
</html>
