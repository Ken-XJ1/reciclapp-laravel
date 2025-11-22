<?php
// admin_reportes_problemas.php
$titulo_pagina = "Reportes de Problemas en Puntos";
$pagina_actual = "admin_reportes_problemas.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

$mensaje_accion = $_SESSION['mensaje_accion_reporte_problema'] ?? null;
unset($_SESSION['mensaje_accion_reporte_problema']);

// Lógica para CAMBIAR ESTADO de un reporte de problema
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reporte_problema'])) {
    $id_reporte = (int)$_POST['id_reporte_problema'];
    $nuevo_estado = $_POST['nuevo_estado_problema'] ?? ''; // 'en_progreso', 'resuelto', 'descartado'
    $observaciones_admin = $_POST['observaciones_admin_problema'] ?? '';

    if ($id_reporte > 0 && !empty($nuevo_estado)) {
        if (!in_array($nuevo_estado, ['en_progreso', 'resuelto', 'descartado'])) {
            $_SESSION['mensaje_accion_reporte_problema'] = ['tipo' => 'error', 'texto' => "Estado de reporte no válido."];
            header("Location: admin_reportes_problemas.php");
            exit();
        }

        // Iniciar transacción
        $conn->begin_transaction();
        try {
            // Actualizar el estado del reporte
            $stmt_update = $conn->prepare("UPDATE reportes_problemas_puntos SET estado_gestion_admin = ?, observaciones_admin = ?, fecha_ultima_gestion = NOW() WHERE id_reporte = ?");
            if (!$stmt_update) {
                throw new Exception("Error al preparar la actualización del reporte: " . $conn->error);
            }
            $stmt_update->bind_param("ssi", $nuevo_estado, $observaciones_admin, $id_reporte);
            if (!$stmt_update->execute()) {
                throw new Exception("Error al ejecutar la actualización del reporte: " . $stmt_update->error);
            }
            $stmt_update->close();

            // Si el problema reportado era 'lleno' y se marca como 'resuelto', podemos cambiar el estado del punto de reciclaje a 'activo'
            if ($nuevo_estado === 'resuelto') {
                $stmt_get_report_info = $conn->prepare("SELECT id_punto_reciclaje FROM reportes_problemas_puntos WHERE id_reporte = ? AND tipo_problema = 'lleno'");
                $stmt_get_report_info->bind_param("i", $id_reporte);
                $stmt_get_report_info->execute();
                $result_report_info = $stmt_get_report_info->get_result();
                if ($row_report_info = $result_report_info->fetch_assoc()) {
                    $id_punto_afectado = $row_report_info['id_punto_reciclaje'];
                    if ($id_punto_afectado) {
                        $stmt_update_punto = $conn->prepare("UPDATE puntos_de_reciclaje SET estado_punto = 'activo', observaciones_admin_punto = CONCAT(IFNULL(observaciones_admin_punto, ''), '\\nProblema ' , ? , ' resuelto por admin.'), fecha_ultima_actualizacion_estado = NOW() WHERE id_punto = ?");
                        if ($stmt_update_punto) {
                            $stmt_update_punto->bind_param("si", $id_reporte, $id_punto_afectado);
                            $stmt_update_punto->execute();
                            $stmt_update_punto->close();
                        } else { error_log("Error al preparar update de punto de reciclaje para problema resuelto: " . $conn->error); }
                    }
                }
                $stmt_get_report_info->close();
            }

            // Registrar en auditoría
            $log_accion = "REPORTE PROBLEMA GESTIONADO: " . strtoupper($nuevo_estado);
            $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $detalles = "Reporte ID: {$id_reporte}. Nuevo estado: {$nuevo_estado}. Observaciones: {$observaciones_admin}";
            $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
            $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'reportes_problemas_puntos', $id_reporte, $detalles, $ip_origen_auditoria);
            $stmt_audit->execute();
            $stmt_audit->close();

            $conn->commit(); // Confirmar la transacción
            $_SESSION['mensaje_accion_reporte_problema'] = ['tipo' => 'success', 'texto' => "Reporte de problema #{$id_reporte} actualizado a '{$nuevo_estado}'."];

        } catch (Exception $e) {
            $conn->rollback(); // Revertir la transacción
            $_SESSION['mensaje_accion_reporte_problema'] = ['tipo' => 'error', 'texto' => "Error al procesar el reporte #{$id_reporte}: " . $e->getMessage()];
            error_log("Error en gestión de reporte de problema: " . $e->getMessage());
        }
    } else {
        $_SESSION['mensaje_accion_reporte_problema'] = ['tipo' => 'error', 'texto' => "Datos de acción incompletos o inválidos."];
    }
    header("Location: admin_reportes_problemas.php");
    exit();
}


// Obtener reportes de problemas (se muestran todos, ordenados por estado y fecha)
$reportes_problemas = [];
$stmt_reportes = $conn->prepare("SELECT
                                    rp.id_reporte,
                                    rp.id_punto_reciclaje,
                                    pr.nombre_punto AS nombre_punto_afectado,
                                    rp.ubicacion_texto_reportada,
                                    rp.id_usuario_reporta,
                                    u.nombre AS usuario_nombre,
                                    u.apellido AS usuario_apellido,
                                    u.email AS usuario_email,
                                    rp.tipo_problema,
                                    rp.comentarios_usuario,
                                    rp.fecha_reporte,
                                    rp.estado_gestion_admin,
                                    rp.observaciones_admin
                                FROM
                                    reportes_problemas_puntos rp
                                LEFT JOIN
                                    puntos_de_reciclaje pr ON rp.id_punto_reciclaje = pr.id_punto
                                JOIN
                                    usuarios u ON rp.id_usuario_reporta = u.id_usuario
                                ORDER BY
                                    FIELD(rp.estado_gestion_admin, 'nuevo', 'en_progreso', 'resuelto', 'descartado'),
                                    rp.fecha_reporte DESC");
if ($stmt_reportes) {
    $stmt_reportes->execute();
    $result = $stmt_reportes->get_result();
    while ($row = $result->fetch_assoc()) {
        $reportes_problemas[] = $row;
    }
    $stmt_reportes->close();
} else {
    error_log("Error al obtener reportes de problemas: " . $conn->error);
    $mensaje_accion = ['tipo' => 'error', 'texto' => 'Error al cargar los reportes de problemas.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Lista de Reportes de Problemas en Puntos de Reciclaje</h3>
    <?php if (!empty($reportes_problemas)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Reporte</th>
                        <th>Punto Afectado</th>
                        <th>Ubicación Alternativa</th>
                        <th>Tipo de Problema</th>
                        <th>Comentarios Usuario</th>
                        <th>Reportado Por</th>
                        <th>Fecha Reporte</th>
                        <th>Estado Actual</th>
                        <th>Observaciones Admin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportes_problemas as $reporte): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reporte['id_reporte']); ?></td>
                            <td>
                                <?php if ($reporte['nombre_punto_afectado']): ?>
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($reporte['nombre_punto_afectado']); ?> (ID: <?php echo htmlspecialchars($reporte['id_punto_reciclaje']); ?>)
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($reporte['ubicacion_texto_reportada'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($reporte['tipo_problema']); ?></td>
                            <td title="<?php echo htmlspecialchars($reporte['comentarios_usuario']); ?>">
                                <?php echo htmlspecialchars(substr($reporte['comentarios_usuario'] ?? '', 0, 70)) . (strlen($reporte['comentarios_usuario'] ?? '') > 70 ? '...' : ''); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($reporte['usuario_nombre'] . ' ' . $reporte['usuario_apellido']); ?><br>
                                <small>(<?php echo htmlspecialchars($reporte['usuario_email']); ?>)</small>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($reporte['fecha_reporte'])); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    if ($reporte['estado_gestion_admin'] === 'nuevo') echo 'danger';
                                    elseif ($reporte['estado_gestion_admin'] === 'en_progreso') echo 'warning';
                                    elseif ($reporte['estado_gestion_admin'] === 'resuelto') echo 'success';
                                    else echo 'info'; // 'descartado'
                                ?>"><?php echo htmlspecialchars($reporte['estado_gestion_admin']); ?></span>
                            </td>
                            <td title="<?php echo htmlspecialchars($reporte['observaciones_admin'] ?? 'Sin observaciones'); ?>">
                                <?php echo htmlspecialchars(substr($reporte['observaciones_admin'] ?? 'Sin obs.', 0, 50)) . (strlen($reporte['observaciones_admin'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalReporteProblema(<?php echo $reporte['id_reporte']; ?>, '<?php echo htmlspecialchars(addslashes($reporte['nombre_punto_afectado'] ?? $reporte['ubicacion_texto_reportada'])); ?>', '<?php echo htmlspecialchars($reporte['estado_gestion_admin']); ?>')"><i class="fas fa-edit"></i> Gestionar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay reportes de problemas pendientes o registrados.</p>
    <?php endif; ?>
</div>

<div id="reporteProblemaModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalReporteProblema()">&times;</span>
        <h2 id="modalReporteProblemaTitle">Gestionar Reporte de Problema</h2>
        <form id="reporteProblemaForm" method="POST" action="admin_reportes_problemas.php">
            <input type="hidden" name="id_reporte_problema" id="modal_id_reporte_problema">
            
            <p>Estás gestionando el reporte #<strong id="modalReporteId"></strong> para: "<strong id="modalPuntoProblema"></strong>".</p>

            <div class="form-group">
                <label for="nuevo_estado_problema">Cambiar Estado a:</label>
                <select id="nuevo_estado_problema" name="nuevo_estado_problema" class="form-control" required>
                    <option value="nuevo">Nuevo</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="resuelto">Resuelto</option>
                    <option value="descartado">Descartado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="observaciones_admin_problema">Observaciones del Administrador (Opcional):</label>
                <textarea id="observaciones_admin_problema" name="observaciones_admin_problema" rows="3" placeholder="Ej: Se ha enviado personal. O, Problema resuelto, contenedor vacío."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalReporteProblema()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModalReporteProblema(idReporte, nombrePunto, estadoActual) {
    document.getElementById('modal_id_reporte_problema').value = idReporte;
    document.getElementById('modalReporteId').textContent = idReporte;
    document.getElementById('modalPuntoProblema').textContent = nombrePunto;
    document.getElementById('nuevo_estado_problema').value = estadoActual; // Selecciona el estado actual

    document.getElementById('reporteProblemaModal').style.display = 'flex';
}

function cerrarModalReporteProblema() {
    document.getElementById('reporteProblemaModal').style.display = 'none';
    document.getElementById('observaciones_admin_problema').value = ''; // Limpiar textarea
}

window.onclick = function(event) {
    const modal = document.getElementById('reporteProblemaModal');
    if (event.target == modal) {
        cerrarModalReporteProblema();
    }
}

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