<?php
// admin_validaciones_recoleccion.php
$titulo_pagina = "Validar Recolecciones de Usuarios"; // Título para la página
$pagina_actual = "admin_validaciones_recoleccion.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

$mensaje_accion = $_SESSION['mensaje_accion_recoleccion'] ?? null;
unset($_SESSION['mensaje_accion_recoleccion']);

// Lógica para APROBAR o RECHAZAR una recolección
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reporte_recoleccion'])) {
    $id_reporte = (int)$_POST['id_reporte_recoleccion'];
    $accion_admin = $_POST['accion_admin'] ?? ''; // 'aprobar' o 'rechazar'
    $observaciones_admin = $_POST['observaciones_admin'] ?? '';
    $puntos_otorgados_manual = isset($_POST['puntos_manual']) ? (int)$_POST['puntos_manual'] : 0; // Puntos si el admin los ajusta

    if ($id_reporte > 0 && !empty($accion_admin)) {
        $nuevo_estado = '';
        $log_accion = '';
        $puntos_finales = 0; // Puntos que se otorgarán/quitarán

        // Obtener puntos propuestos por el usuario y el ID del usuario para auditoría y actualización
        $stmt_get_info = $conn->prepare("SELECT id_usuario, puntos_otorgados FROM reportes_recoleccion_usuario WHERE id_reporte_recoleccion = ?");
        $stmt_get_info->bind_param("i", $id_reporte);
        $stmt_get_info->execute();
        $result_info = $stmt_get_info->get_result();
        $reporte_info = $result_info->fetch_assoc();
        $stmt_get_info->close();

        if (!$reporte_info) {
            $_SESSION['mensaje_accion_recoleccion'] = ['tipo' => 'error', 'texto' => "Reporte de recolección #{$id_reporte} no encontrado."];
            header("Location: admin_validaciones_recoleccion.php");
            exit();
        }

        $id_usuario_reporte = $reporte_info['id_usuario'];
        $puntos_previstos_reporte = $reporte_info['puntos_otorgados']; // Puntos calculados provisionalmente por el usuario

        if ($accion_admin === 'aprobar') {
            $nuevo_estado = 'aprobada';
            $log_accion = 'RECOLECCIÓN APROBADA';
            // Si el admin ingresó puntos manualmente, úsalos; de lo contrario, usa los puntos previstos
            $puntos_finales = ($puntos_otorgados_manual > 0) ? $puntos_otorgados_manual : $puntos_previstos_reporte;
            $_SESSION['mensaje_accion_recoleccion'] = ['tipo' => 'success', 'texto' => "Reporte #{$id_reporte} aprobado. Se otorgaron {$puntos_finales} puntos."];
        } elseif ($accion_admin === 'rechazar') {
            $nuevo_estado = 'rechazada_definitiva';
            $log_accion = 'RECOLECCIÓN RECHAZADA';
            $puntos_finales = 0; // No se otorgan puntos al rechazar
            $_SESSION['mensaje_accion_recoleccion'] = ['tipo' => 'info', 'texto' => "Reporte #{$id_reporte} rechazado."];
        }

        if (!empty($nuevo_estado)) {
            // Iniciar transacción
            $conn->begin_transaction();

            try {
                // 1. Actualizar el estado del reporte de recolección
                $stmt_update_reporte = $conn->prepare("UPDATE reportes_recoleccion_usuario SET estado_validacion_admin = ?, observaciones_admin = ?, fecha_validacion_admin = NOW(), puntos_otorgados = ? WHERE id_reporte_recoleccion = ? AND estado_validacion_admin = 'pendiente'");
                if (!$stmt_update_reporte) {
                    throw new Exception("Error al preparar update de reporte: " . $conn->error);
                }
                $stmt_update_reporte->bind_param("ssii", $nuevo_estado, $observaciones_admin, $puntos_finales, $id_reporte);
                if (!$stmt_update_reporte->execute()) {
                    throw new Exception("Error al ejecutar update de reporte: " . $stmt_update_reporte->error);
                }
                $stmt_update_reporte->close();

                // 2. Actualizar los puntos del usuario si la recolección fue aprobada
                if ($accion_admin === 'aprobar' && $puntos_finales > 0) {
                    $stmt_update_puntos = $conn->prepare("UPDATE usuarios SET puntos_acumulados = puntos_acumulados + ? WHERE id_usuario = ?");
                    if (!$stmt_update_puntos) {
                        throw new Exception("Error al preparar update de puntos: " . $conn->error);
                    }
                    $stmt_update_puntos->bind_param("is", $puntos_finales, $id_usuario_reporte);
                    if (!$stmt_update_puntos->execute()) {
                        throw new Exception("Error al ejecutar update de puntos: " . $stmt_update_puntos->error);
                    }
                    $stmt_update_puntos->close();
                }

                // 3. Registrar en auditoría
                // Aquí deberías tener una función `registrar_auditoria` accesible globalmente
                // Por ejemplo, si tienes un archivo `funciones_auditoria.php` que incluyes:
                // include_once 'funciones_auditoria.php';
                // registrar_auditoria($conn, $_SESSION['user_id'], $log_accion, "Reporte Recolección ID: {$id_reporte}. Usuario: {$id_usuario_reporte}. Puntos: {$puntos_finales}. Observaciones: {$observaciones_admin}", 'reportes_recoleccion_usuario', $id_reporte);
                // Si no tienes una función global, puedes insertar directamente (pero es mejor una función para consistencia)
                $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
                $detalles = "Reporte Recolección ID: {$id_reporte}. Usuario: {$id_usuario_reporte}. Puntos: {$puntos_finales}. Observaciones: {$observaciones_admin}";
                $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
                $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'reportes_recoleccion_usuario', $id_reporte, $detalles, $ip_origen_auditoria);
                $stmt_audit->execute();
                $stmt_audit->close();

                $conn->commit(); // Confirmar la transacción
            } catch (Exception $e) {
                $conn->rollback(); // Revertir la transacción en caso de error
                $_SESSION['mensaje_accion_recoleccion'] = ['tipo' => 'error', 'texto' => "Error al procesar la acción para el reporte #{$id_reporte}: " . $e->getMessage()];
                error_log("Error en transacción de validación de recolección: " . $e->getMessage());
            }
        }
    } else {
        $_SESSION['mensaje_accion_recoleccion'] = ['tipo' => 'error', 'texto' => "Acción o ID de reporte inválido."];
    }
    header("Location: admin_validaciones_recoleccion.php"); // Redirigir para evitar reenvío de formulario
    exit();
}


// Obtener reportes de recolección pendientes de validación
$reportes_pendientes = [];
$stmt_reportes = $conn->prepare("SELECT
                                    rr.id_reporte_recoleccion,
                                    rr.id_usuario,
                                    u.nombre AS usuario_nombre,
                                    u.apellido AS usuario_apellido,
                                    u.email AS usuario_email,
                                    rr.id_punto_reciclaje_destino,
                                    pr.nombre_punto AS punto_destino_nombre,
                                    rr.fecha_recoleccion_usuario,
                                    rr.descripcion_general_materiales,
                                    rr.cantidad_total_kg_aprox,
                                    rr.recoleccion_fue_correcta,
                                    rr.descripcion_problema_usuario,
                                    rr.ruta_evidencia_foto,
                                    rr.fecha_reporte_sistema,
                                    rr.puntos_otorgados AS puntos_sugeridos,
                                    tr.nombre AS tipo_residuo_nombre,
                                    tr.puntos_por_kg
                                FROM
                                    reportes_recoleccion_usuario rr
                                JOIN
                                    usuarios u ON rr.id_usuario = u.id_usuario
                                LEFT JOIN
                                    puntos_de_reciclaje pr ON rr.id_punto_reciclaje_destino = pr.id_punto
                                LEFT JOIN
                                    detalle_reporte_recoleccion dr ON rr.id_reporte_recoleccion = dr.id_reporte_recoleccion
                                LEFT JOIN
                                    tipos_residuos tr ON dr.id_tipo_residuo = tr.id_tipo_residuo
                                WHERE
                                    rr.estado_validacion_admin = 'pendiente'
                                ORDER BY
                                    rr.fecha_reporte_sistema DESC");

if ($stmt_reportes) {
    $stmt_reportes->execute();
    $result = $stmt_reportes->get_result();
    while ($row = $result->fetch_assoc()) {
        $reportes_pendientes[] = $row;
    }
    $stmt_reportes->close();
} else {
    error_log("Error al obtener reportes de recolección: " . $conn->error);
    $mensaje_accion = ['tipo' => 'error', 'texto' => 'Error al cargar los reportes de recolección. Revise los logs del servidor.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Reportes de Recolección Pendientes de Validación</h3>
    <?php if (!empty($reportes_pendientes)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Reporte</th>
                        <th>Usuario</th>
                        <th>Punto Destino</th>
                        <th>Fecha Recolección</th>
                        <th>Materiales (Tipo/Desc)</th>
                        <th>Cantidad (Kg)</th>
                        <th>Puntos Sugeridos</th>
                        <th>Reporte Correcto</th>
                        <th>Problema Reportado</th>
                        <th>Evidencia</th>
                        <th>Fecha Sistema</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportes_pendientes as $reporte): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reporte['id_reporte_recoleccion']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($reporte['usuario_nombre'] . ' ' . $reporte['usuario_apellido']); ?>
                                <small>(<?php echo htmlspecialchars($reporte['usuario_email']); ?>)</small>
                            </td>
                            <td><?php echo htmlspecialchars($reporte['punto_destino_nombre'] ?? 'No especificado'); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($reporte['fecha_recoleccion_usuario'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($reporte['tipo_residuo_nombre'] ?? 'N/A'); ?></strong><br>
                                <?php echo htmlspecialchars(substr($reporte['descripcion_general_materiales'] ?? '', 0, 50)) . (strlen($reporte['descripcion_general_materiales'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td><?php echo htmlspecialchars(number_format($reporte['cantidad_total_kg_aprox'], 2)); ?></td>
                             <td><?php echo htmlspecialchars($reporte['puntos_sugeridos']); ?></td>
                            <td>
                                <?php if ($reporte['recoleccion_fue_correcta']): ?>
                                    <span style="color: green;">Sí</span>
                                <?php else: ?>
                                    <span style="color: orange;">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($reporte['descripcion_problema_usuario'])): ?>
                                    <span title="<?php echo htmlspecialchars($reporte['descripcion_problema_usuario']); ?>">Ver Detalle</span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($reporte['ruta_evidencia_foto'])): ?>
                                    <a href="<?php echo htmlspecialchars($reporte['ruta_evidencia_foto']); ?>" target="_blank" class="btn-small-link"><i class="fas fa-image"></i> Ver</a>
                                <?php else: ?>
                                    No
                                <?php endif; ?>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($reporte['fecha_reporte_sistema'])); ?></td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalRecoleccion(<?php echo $reporte['id_reporte_recoleccion']; ?>, '<?php echo htmlspecialchars(addslashes($reporte['usuario_nombre'] . ' ' . $reporte['usuario_apellido'])); ?>', 'aprobar', <?php echo $reporte['puntos_sugeridos']; ?>)"><i class="fas fa-check" style="color:var(--admin-secondary-color);"></i> Aprobar</button>
                                <button class="btn-accion-modal" onclick="abrirModalRecoleccion(<?php echo $reporte['id_reporte_recoleccion']; ?>, '<?php echo htmlspecialchars(addslashes($reporte['usuario_nombre'] . ' ' . $reporte['usuario_apellido'])); ?>', 'rechazar', 0)"><i class="fas fa-times" style="color:var(--admin-danger-color);"></i> Rechazar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay reportes de recolección pendientes de validación.</p>
    <?php endif; ?>
</div>

<div id="recoleccionModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalRecoleccion()">&times;</span>
        <h2 id="modalRecoleccionTitle">Gestionar Reporte de Recolección</h2>
        <form id="recoleccionForm" method="POST" action="admin_validaciones_recoleccion.php">
            <input type="hidden" name="id_reporte_recoleccion" id="modal_id_reporte_recoleccion">
            <input type="hidden" name="accion_admin" id="modal_accion_admin">

            <p>Estás a punto de <strong id="modalAccionTextoRecoleccion"></strong> el reporte de recolección de: "<strong id="modalNombreUsuarioRecoleccion"></strong>".</p>
            <p id="puntosSugeridosTexto" style="display:none;">Puntos sugeridos: <strong id="modalPuntosSugeridos"></strong>.</p>

            <div class="form-group" id="puntosManualGroup" style="display:none;">
                <label for="puntos_manual">Ajustar Puntos a Otorgar (opcional, solo para APROBAR):</label>
                <input type="number" id="puntos_manual" name="puntos_manual" min="0" placeholder="Deja vacío para usar puntos sugeridos">
            </div>

            <div class="form-group">
                <label for="observaciones_admin">Observaciones del Administrador (Opcional):</label>
                <textarea id="observaciones_admin" name="observaciones_admin" rows="3" placeholder="Ej: Reporte verificado y aprobado. O, Motivo del rechazo..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="modalSubmitButtonRecoleccion">Confirmar</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalRecoleccion()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<style>
/* Estilos adicionales para los botones en la tabla si es necesario */
.btn-accion-modal {
    background: none;
    border: none;
    padding: 5px 8px;
    cursor: pointer;
    font-size: 0.85rem;
    color: var(--admin-primary-color);
    display: inline-flex;
    align-items: center;
    margin-bottom: 5px; /* Espacio si se apilan */
}
.btn-accion-modal i {
    margin-right: 5px;
}
.btn-accion-modal:hover {
    text-decoration: underline;
}
.btn-small-link {
    font-size: 0.8rem;
    padding: 3px 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-decoration: none;
    color: #3498db;
}
.btn-small-link:hover {
    background-color: #f0f0f0;
}
</style>

<script>
function abrirModalRecoleccion(idReporte, nombreUsuario, accion, puntosSugeridos) {
    document.getElementById('modal_id_reporte_recoleccion').value = idReporte;
    document.getElementById('modal_accion_admin').value = accion;
    document.getElementById('modalNombreUsuarioRecoleccion').textContent = nombreUsuario;
    const modalSubmitButton = document.getElementById('modalSubmitButtonRecoleccion');
    const modalAccionTexto = document.getElementById('modalAccionTextoRecoleccion');
    const puntosManualGroup = document.getElementById('puntosManualGroup');
    const puntosManualInput = document.getElementById('puntos_manual');
    const puntosSugeridosTextoDiv = document.getElementById('puntosSugeridosTexto');
    const modalPuntosSugeridosSpan = document.getElementById('modalPuntosSugeridos');

    if (accion === 'aprobar') {
        modalAccionTexto.textContent = "APROBAR";
        modalSubmitButton.textContent = "Aprobar Recolección";
        modalSubmitButton.className = 'btn btn-secondary'; // Clase verde
        puntosManualGroup.style.display = 'block'; // Mostrar ajuste de puntos
        puntosManualInput.value = puntosSugeridos; // Establecer puntos sugeridos por defecto
        modalPuntosSugeridosSpan.textContent = puntosSugeridos; // Mostrar puntos sugeridos
        puntosSugeridosTextoDiv.style.display = 'block'; // Mostrar el texto de puntos sugeridos

    } else if (accion === 'rechazar') {
        modalAccionTexto.textContent = "RECHAZAR";
        modalSubmitButton.textContent = "Rechazar Recolección";
        modalSubmitButton.className = 'btn btn-danger'; // Clase roja
        puntosManualGroup.style.display = 'none'; // Ocultar ajuste de puntos
        puntosManualInput.value = ''; // Limpiar el campo
        puntosManualInput.required = false; // No requerido
        puntosSugeridosTextoDiv.style.display = 'none'; // Ocultar el texto de puntos sugeridos
    }
    document.getElementById('recoleccionModal').style.display = 'flex';
}

function cerrarModalRecoleccion() {
    document.getElementById('recoleccionModal').style.display = 'none';
    document.getElementById('observaciones_admin').value = ''; // Limpiar textarea
    document.getElementById('puntos_manual').value = ''; // Limpiar puntos
}

// Cerrar modal si se hace clic fuera del contenido
window.onclick = function(event) {
    const modal = document.getElementById('recoleccionModal');
    if (event.target == modal) {
        cerrarModalRecoleccion();
    }
}

// Para manejar mensajes de sesión con JS (opcional, pero mejora la UX)
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 600); // Tiempo para la transición de opacidad
        }, 5000); // El mensaje desaparece después de 5 segundos
    });
});
</script>

<?php
// Cierre de la conexión y del layout
if (isset($conn) && $conn && $conn->ping()) {
    $conn->close();
}
// Asumo que admin_layout.php abre las etiquetas principales y este archivo es el "contenido".
// Deberías tener una estructura donde admin_layout.php abre los tags principales
// y un admin_footer.php o similar los cierra después de incluir el contenido de esta página,
// o que admin_layout.php mismo los cierra.
?>
            </main> </div> </div> </body>
</html>