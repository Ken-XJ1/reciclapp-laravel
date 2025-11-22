<?php
// admin_propuestas_puntos.php
$titulo_pagina = "Propuestas de Nuevos Puntos de Reciclaje";
$pagina_actual = "admin_propuestas_puntos.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

$mensaje_accion = $_SESSION['mensaje_accion_punto'] ?? null;
unset($_SESSION['mensaje_accion_punto']);

// Lógica para APROBAR o RECHAZAR una propuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_punto_propuesto'])) {
    $id_punto = (int)$_POST['id_punto_propuesto'];
    $accion_admin = $_POST['accion_admin'] ?? ''; // 'aprobar' o 'rechazar'
    $observaciones_admin = $_POST['observaciones_admin_propuesta'] ?? '';

    if ($id_punto > 0 && !empty($accion_admin)) {
        $nuevo_estado = '';
        $log_accion = '';

        if ($accion_admin === 'aprobar') {
            $nuevo_estado = 'activo';
            $log_accion = 'PROPUESTA DE PUNTO APROBADA';
            $_SESSION['mensaje_accion_punto'] = ['tipo' => 'success', 'texto' => "Propuesta de punto #{$id_punto} aprobada y activada."];
        } elseif ($accion_admin === 'rechazar') {
            $nuevo_estado = 'inactivo'; // O podrías tener un estado 'rechazado'
            $log_accion = 'PROPUESTA DE PUNTO RECHAZADA';
            $_SESSION['mensaje_accion_punto'] = ['tipo' => 'info', 'texto' => "Propuesta de punto #{$id_punto} rechazada."];
        }

        if (!empty($nuevo_estado)) {
            $stmt_update = $conn->prepare("UPDATE puntos_de_reciclaje SET estado_punto = ?, observaciones_admin_punto = ?, fecha_ultima_actualizacion_estado = NOW() WHERE id_punto = ? AND estado_punto = 'pendiente_aprobacion'");
            if ($stmt_update) {
                $stmt_update->bind_param("ssi", $nuevo_estado, $observaciones_admin, $id_punto);
                if (!$stmt_update->execute()) {
                    error_log("Error al actualizar propuesta de punto #{$id_punto}: " . $stmt_update->error);
                    $_SESSION['mensaje_accion_punto'] = ['tipo' => 'error', 'texto' => "Error al procesar la acción para el punto #{$id_punto}."];
                }
                $stmt_update->close();
                
                // Registrar en auditoría
                // (Asegúrate que la función registrar_auditoria esté disponible, quizás en admin_layout.php o un archivo de funciones)
                // Si no la tienes global, define una versión simple aquí o incluye el archivo que la contenga.
                // Ejemplo simple de cómo podría ser la llamada:
                // registrar_auditoria_admin($conn, $_SESSION['user_id'], $log_accion, "Punto ID: {$id_punto}. Observaciones: {$observaciones_admin}", 'puntos_de_reciclaje', $id_punto);

            } else {
                error_log("Error al preparar update para propuesta de punto #{$id_punto}: " . $conn->error);
                $_SESSION['mensaje_accion_punto'] = ['tipo' => 'error', 'texto' => "Error al preparar la acción para el punto #{$id_punto}."];
            }
        }
    } else {
        $_SESSION['mensaje_accion_punto'] = ['tipo' => 'error', 'texto' => "Acción o ID de punto inválido."];
    }
    header("Location: admin_propuestas_puntos.php"); // Redirigir para evitar reenvío de formulario
    exit();
}


// Obtener propuestas pendientes
$propuestas_pendientes = [];
$stmt_propuestas = $conn->prepare("SELECT p.id_punto, p.nombre_punto, p.descripcion_punto, p.latitud, p.longitud, p.direccion_aproximada, p.tipos_materiales_info, p.fecha_propuesta, u.nombre AS proponente_nombre, u.apellido AS proponente_apellido, u.email AS proponente_email
                                   FROM puntos_de_reciclaje p
                                   LEFT JOIN usuarios u ON p.id_usuario_propone = u.id_usuario
                                   WHERE p.estado_punto = 'pendiente_aprobacion'
                                   ORDER BY p.fecha_propuesta DESC");
if ($stmt_propuestas) {
    $stmt_propuestas->execute();
    $result = $stmt_propuestas->get_result();
    while ($row = $result->fetch_assoc()) {
        $propuestas_pendientes[] = $row;
    }
    $stmt_propuestas->close();
} else {
    error_log("Error al obtener propuestas de puntos: " . $conn->error);
    $mensaje_accion = ['tipo' => 'error', 'texto' => 'Error al cargar las propuestas. Revise los logs del servidor.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Propuestas de Puntos Pendientes de Aprobación</h3>
    <?php if (!empty($propuestas_pendientes)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Propuesto</th>
                        <th>Descripción</th>
                        <th>Lat/Lon</th>
                        <th>Dirección</th>
                        <th>Materiales</th>
                        <th>Propuesto por</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($propuestas_pendientes as $propuesta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($propuesta['id_punto']); ?></td>
                            <td><?php echo htmlspecialchars($propuesta['nombre_punto']); ?></td>
                            <td title="<?php echo htmlspecialchars($propuesta['descripcion_punto']); ?>">
                                <?php echo htmlspecialchars(substr($propuesta['descripcion_punto'] ?? '', 0, 50)) . (strlen($propuesta['descripcion_punto'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(number_format((float)$propuesta['latitud'], 5)) . ', ' . htmlspecialchars(number_format((float)$propuesta['longitud'], 5)); ?>
                                <a href="https://www.openstreetmap.org/?mlat=<?php echo htmlspecialchars($propuesta['latitud']); ?>&mlon=<?php echo htmlspecialchars($propuesta['longitud']); ?>#map=16/<?php echo htmlspecialchars($propuesta['latitud']); ?>/<?php echo htmlspecialchars($propuesta['longitud']); ?>" target="_blank" title="Ver en mapa"><i class="fas fa-map-marker-alt" style="margin-left:5px;"></i></a>
                            </td>
                            <td><?php echo htmlspecialchars($propuesta['direccion_aproximada'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($propuesta['tipos_materiales_info'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($propuesta['proponente_nombre']): ?>
                                    <?php echo htmlspecialchars($propuesta['proponente_nombre'] . ' ' . $propuesta['proponente_apellido']); ?>
                                    <small>(<?php echo htmlspecialchars($propuesta['proponente_email']); ?>)</small>
                                <?php else: ?>
                                    Sistema/Admin
                                <?php endif; ?>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($propuesta['fecha_propuesta'])); ?></td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalPropuesta(<?php echo $propuesta['id_punto']; ?>, '<?php echo htmlspecialchars(addslashes($propuesta['nombre_punto'])); ?>', 'aprobar')"><i class="fas fa-check" style="color:var(--admin-secondary-color);"></i> Aprobar</button>
                                <button class="btn-accion-modal" onclick="abrirModalPropuesta(<?php echo $propuesta['id_punto']; ?>, '<?php echo htmlspecialchars(addslashes($propuesta['nombre_punto'])); ?>', 'rechazar')"><i class="fas fa-times" style="color:var(--admin-danger-color);"></i> Rechazar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay nuevas propuestas de puntos de reciclaje pendientes de revisión.</p>
    <?php endif; ?>
</div>

<div id="propuestaModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalPropuesta()">&times;</span>
        <h2 id="modalPropuestaTitle">Gestionar Propuesta de Punto</h2>
        <form id="propuestaForm" method="POST" action="admin_propuestas_puntos.php">
            <input type="hidden" name="id_punto_propuesto" id="modal_id_punto_propuesto">
            <input type="hidden" name="accion_admin" id="modal_accion_admin">
            
            <p>Estás a punto de <strong id="modalAccionTexto"></strong> la propuesta para el punto: "<strong id="modalNombrePunto"></strong>".</p>

            <div class="form-group">
                <label for="observaciones_admin_propuesta">Observaciones del Administrador (Opcional):</label>
                <textarea id="observaciones_admin_propuesta" name="observaciones_admin_propuesta" rows="3" placeholder="Ej: Punto verificado y activado. O, Razón del rechazo..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="modalSubmitButton">Confirmar</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalPropuesta()" style="margin-left:10px;">Cancelar</button>
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
</style>

<script>
function abrirModalPropuesta(idPunto, nombrePunto, accion) {
    document.getElementById('modal_id_punto_propuesto').value = idPunto;
    document.getElementById('modal_accion_admin').value = accion;
    document.getElementById('modalNombrePunto').textContent = nombrePunto;
    const modalSubmitButton = document.getElementById('modalSubmitButton');
    const modalAccionTexto = document.getElementById('modalAccionTexto');

    if (accion === 'aprobar') {
        modalAccionTexto.textContent = "APROBAR";
        modalSubmitButton.textContent = "Aprobar Propuesta";
        modalSubmitButton.className = 'btn btn-secondary'; // Clase verde
    } else if (accion === 'rechazar') {
        modalAccionTexto.textContent = "RECHAZAR";
        modalSubmitButton.textContent = "Rechazar Propuesta";
        modalSubmitButton.className = 'btn btn-danger'; // Clase roja
    }
    document.getElementById('propuestaModal').style.display = 'flex';
}

function cerrarModalPropuesta() {
    document.getElementById('propuestaModal').style.display = 'none';
    document.getElementById('observaciones_admin_propuesta').value = ''; // Limpiar textarea
}

// Cerrar modal si se hace clic fuera del contenido
window.onclick = function(event) {
    const modal = document.getElementById('propuestaModal');
    if (event.target == modal) {
        cerrarModalPropuesta();
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
// Si tu admin_layout.php no cierra las etiquetas HTML, hazlo aquí.
// Asumo que admin_layout.php es incluido al principio y este archivo es el "contenido".
// Deberías tener una estructura donde admin_layout.php abre los tags principales
// y un admin_footer.php o similar los cierra después de incluir el contenido de esta página.
// Por ahora, si sigues el patrón de incluir layout al principio y luego poner esto:
?>
            </main> </div> </div> </body>
</html>