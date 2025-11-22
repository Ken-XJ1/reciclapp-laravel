<?php
// admin_actividades_flujo.php
$titulo_pagina = "Actividades y Flujos del Sistema";
$pagina_actual = "admin_actividades_flujo.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

// Obtener las últimas actividades (ej: reportes de recolección y propuestas de puntos)
$ultimas_actividades = [];

if (isset($conn) && $conn && !$conn->connect_error) {
    // Actividad: Reportes de Recolección (últimos 20)
    $stmt_recolecciones = $conn->prepare("SELECT
                                            rr.id_reporte_recoleccion AS id_actividad,
                                            'recoleccion' AS tipo_actividad,
                                            rr.fecha_reporte_sistema AS fecha_actividad,
                                            CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre,
                                            rr.descripcion_general_materiales AS detalles,
                                            rr.estado_validacion_admin AS estado
                                        FROM
                                            reportes_recoleccion_usuario rr
                                        JOIN
                                            usuarios u ON rr.id_usuario = u.id_usuario
                                        ORDER BY
                                            rr.fecha_reporte_sistema DESC
                                        LIMIT 20");
    if ($stmt_recolecciones) {
        $stmt_recolecciones->execute();
        $result = $stmt_recolecciones->get_result();
        while ($row = $result->fetch_assoc()) {
            $ultimas_actividades[] = $row;
        }
        $stmt_recolecciones->close();
    } else {
        error_log("Error al obtener actividades de recolección: " . $conn->error);
    }

    // Actividad: Propuestas de Puntos (últimos 20)
    $stmt_propuestas = $conn->prepare("SELECT
                                            p.id_punto AS id_actividad,
                                            'propuesta_punto' AS tipo_actividad,
                                            p.fecha_propuesta AS fecha_actividad,
                                            CONCAT(u.nombre, ' ', u.apellido) AS usuario_nombre,
                                            p.nombre_punto AS detalles,
                                            p.estado_punto AS estado
                                        FROM
                                            puntos_de_reciclaje p
                                        LEFT JOIN
                                            usuarios u ON p.id_usuario_propone = u.id_usuario
                                        ORDER BY
                                            p.fecha_propuesta DESC
                                        LIMIT 20");
    if ($stmt_propuestas) {
        $stmt_propuestas->execute();
        $result = $stmt_propuestas->get_result();
        while ($row = $result->fetch_assoc()) {
            $ultimas_actividades[] = $row;
        }
        $stmt_propuestas->close();
    } else {
        error_log("Error al obtener actividades de propuestas de puntos: " . $conn->error);
    }

    // Opcional: Ordenar todas las actividades combinadas por fecha
    usort($ultimas_actividades, function($a, $b) {
        return strtotime($b['fecha_actividad']) - strtotime($a['fecha_actividad']);
    });

} else {
    // Manejo de error de conexión si $conn no está disponible
    // Esto ya se maneja en conexion.php, pero para el display al usuario
    $_SESSION['mensaje_accion_actividad'] = ['tipo' => 'error', 'texto' => 'Error de conexión a la base de datos. No se pueden cargar las actividades.'];
}

?>

<?php if (isset($_SESSION['mensaje_accion_actividad'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['mensaje_accion_actividad']['tipo']); ?>">
        <?php echo htmlspecialchars($_SESSION['mensaje_accion_actividad']['texto']); ?>
    </div>
    <?php unset($_SESSION['mensaje_accion_actividad']); ?>
<?php endif; ?>

<div class="card">
    <h3>Últimas Actividades del Sistema</h3>
    <p>Aquí puedes ver un resumen de las acciones clave de los usuarios y el sistema.</p>
    <?php if (!empty($ultimas_actividades)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo de Actividad</th>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Usuario / Origen</th>
                        <th>Detalles</th>
                        <th>Estado</th>
                        <th>Ver Más</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_actividades as $actividad): ?>
                        <tr>
                            <td>
                                <?php
                                    if ($actividad['tipo_actividad'] == 'recoleccion') echo '<i class="fas fa-box-open" style="color: var(--admin-primary-color);"></i> Reporte Recolección';
                                    elseif ($actividad['tipo_actividad'] == 'propuesta_punto') echo '<i class="fas fa-map-pin" style="color: var(--admin-secondary-color);"></i> Propuesta Punto';
                                    else echo htmlspecialchars($actividad['tipo_actividad']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($actividad['id_actividad']); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($actividad['fecha_actividad'])); ?></td>
                            <td><?php echo htmlspecialchars($actividad['usuario_nombre'] ?? 'Sistema'); ?></td>
                            <td>
                                <?php echo htmlspecialchars(substr($actividad['detalles'] ?? '', 0, 70)) . (strlen($actividad['detalles'] ?? '') > 70 ? '...' : ''); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php
                                    if ($actividad['estado'] === 'aprobada' || $actividad['estado'] === 'activo') echo 'success';
                                    elseif ($actividad['estado'] === 'pendiente' || $actividad['estado'] === 'pendiente_aprobacion') echo 'warning';
                                    elseif ($actividad['estado'] === 'rechazada_definitiva' || $actividad['estado'] === 'inactivo') echo 'danger';
                                    else echo 'info';
                                ?>"><?php echo htmlspecialchars($actividad['estado']); ?></span>
                            </td>
                            <td>
                                <?php if ($actividad['tipo_actividad'] == 'recoleccion'): ?>
                                    <a href="admin_validaciones_recoleccion.php" class="btn-small-link">Gestionar</a>
                                <?php elseif ($actividad['tipo_actividad'] == 'propuesta_punto'): ?>
                                    <a href="admin_propuestas_puntos.php" class="btn-small-link">Revisar</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No se encontraron actividades recientes.</p>
    <?php endif; ?>
</div>

<script>
// Para manejar mensajes de sesión con JS
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 600);
        }, 5000);
    });
});
</script>

<?php
if (isset($conn) && $conn && $conn->ping()) {
    $conn->close();
}
?>
            </main> </div> </div> </body>
</html>