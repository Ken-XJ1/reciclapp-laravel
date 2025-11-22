<?php
// admin_premios_flujo.php
$titulo_pagina = "Gestión de Premios y Canjes";
$pagina_actual = "admin_premios_flujo.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

$mensaje_accion = $_SESSION['mensaje_accion_premio_admin'] ?? null;
unset($_SESSION['mensaje_accion_premio_admin']);

// Lógica para AGREGAR/EDITAR premio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action_premio']) && ($_POST['action_premio'] === 'add' || $_POST['action_premio'] === 'edit'))) {
    $id_premio = (int)($_POST['id_premio_edit'] ?? 0);
    $nombre_premio = $_POST['nombre_premio'] ?? null;
    $descripcion_premio = $_POST['descripcion_premio'] ?? null;
    $puntos_requeridos = (int)($_POST['puntos_requeridos'] ?? 0);
    $stock_disponible = isset($_POST['stock_disponible']) && $_POST['stock_disponible'] !== '' ? (int)$_POST['stock_disponible'] : null;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Manejo de la imagen de premio
    $ruta_imagen = '';
    $upload_dir = 'media/premios/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['imagen_premio']) && $_FILES['imagen_premio']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['imagen_premio']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['imagen_premio']['name']);
        $destination = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            $ruta_imagen = $destination;
        } else {
            $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Error al subir la imagen."];
            header("Location: admin_premios_flujo.php");
            exit();
        }
    } else if ($_POST['action_premio'] === 'edit' && isset($_POST['current_imagen_premio'])) {
        $ruta_imagen = $_POST['current_imagen_premio']; // Mantener la imagen actual si no se sube una nueva
    }


    if (empty($nombre_premio) || $puntos_requeridos <= 0) {
        $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Nombre del premio y puntos requeridos son obligatorios."];
    } else {
        $conn->begin_transaction();
        try {
            if ($_POST['action_premio'] === 'add') {
                $stmt = $conn->prepare("INSERT INTO premios (nombre_premio, descripcion_premio, puntos_requeridos, stock_disponible, activo, ruta_imagen_premio) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt) throw new Exception("Error al preparar ADD premio: " . $conn->error);
                $stmt->bind_param("ssiiss", $nombre_premio, $descripcion_premio, $puntos_requeridos, $stock_disponible, $activo, $ruta_imagen);
                if (!$stmt->execute()) throw new Exception("Error al ejecutar ADD premio: " . $stmt->error);
                $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'success', 'texto' => "Premio '{$nombre_premio}' agregado con éxito."];
                $log_accion = "PREMIO AGREGADO";
                $id_registro_modificado = $conn->insert_id;
            } elseif ($_POST['action_premio'] === 'edit') {
                $sql = "UPDATE premios SET nombre_premio = ?, descripcion_premio = ?, puntos_requeridos = ?, stock_disponible = ?, activo = ?";
                if (!empty($ruta_imagen)) {
                    $sql .= ", ruta_imagen_premio = ?";
                }
                $sql .= " WHERE id_premio = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new Exception("Error al preparar EDIT premio: " . $conn->error);

                if (!empty($ruta_imagen)) {
                    $stmt->bind_param("ssiisii", $nombre_premio, $descripcion_premio, $puntos_requeridos, $stock_disponible, $activo, $ruta_imagen, $id_premio);
                } else {
                    $stmt->bind_param("ssiisi", $nombre_premio, $descripcion_premio, $puntos_requeridos, $stock_disponible, $activo, $id_premio);
                }
                
                if (!$stmt->execute()) throw new Exception("Error al ejecutar EDIT premio: " . $stmt->error);
                $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'success', 'texto' => "Premio '{$nombre_premio}' actualizado con éxito."];
                $log_accion = "PREMIO EDITADO";
                $id_registro_modificado = $id_premio;
            }
            $stmt->close();

            // Registrar en auditoría
            $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $detalles = "Premio: {$nombre_premio}. Puntos: {$puntos_requeridos}. Stock: {$stock_disponible}. Activo: {$activo}. Imagen: {$ruta_imagen}";
            $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
            $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'premios', $id_registro_modificado, $detalles, $ip_origen_auditoria);
            $stmt_audit->execute();
            $stmt_audit->close();
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Error en la operación del premio: " . $e->getMessage()];
            error_log("Error en gestión de premios (add/edit): " . $e->getMessage());
        }
    }
    header("Location: admin_premios_flujo.php");
    exit();
}

// Lógica para CAMBIAR ESTADO de un canje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_canje_gestion'])) {
    $id_canje = (int)$_POST['id_canje_gestion'];
    $nuevo_estado_canje = $_POST['nuevo_estado_canje'] ?? ''; // 'entregado', 'cancelado'
    $observaciones_admin_canje = $_POST['observaciones_admin_canje'] ?? '';

    if ($id_canje > 0 && !empty($nuevo_estado_canje)) {
        if (!in_array($nuevo_estado_canje, ['entregado', 'cancelado'])) {
            $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Estado de canje no válido."];
            header("Location: admin_premios_flujo.php");
            exit();
        }

        $conn->begin_transaction();
        try {
            $stmt_update_canje = $conn->prepare("UPDATE canjes_usuario SET estado_canje = ?, observaciones_admin = ?, fecha_gestion_admin = NOW() WHERE id_canje = ?");
            if (!$stmt_update_canje) throw new Exception("Error al preparar update de canje: " . $conn->error);
            $stmt_update_canje->bind_param("ssi", $nuevo_estado_canje, $observaciones_admin_canje, $id_canje);
            if (!$stmt_update_canje->execute()) throw new Exception("Error al ejecutar update de canje: " . $stmt_update_canje->error);
            $stmt_update_canje->close();

            // Si se cancela un canje, devolver los puntos al usuario y el stock al premio
            if ($nuevo_estado_canje === 'cancelado') {
                $stmt_get_canje_info = $conn->prepare("SELECT id_usuario, id_premio, puntos_usados FROM canjes_usuario WHERE id_canje = ?");
                $stmt_get_canje_info->bind_param("i", $id_canje);
                $stmt_get_canje_info->execute();
                $result_canje_info = $stmt_get_canje_info->get_result();
                $canje_info = $result_canje_info->fetch_assoc();
                $stmt_get_canje_info->close();

                if ($canje_info) {
                    // Devolver puntos al usuario
                    $stmt_return_points = $conn->prepare("UPDATE usuarios SET puntos_acumulados = puntos_acumulados + ? WHERE id_usuario = ?");
                    if (!$stmt_return_points) throw new Exception("Error al preparar devolución puntos: " . $conn->error);
                    $stmt_return_points->bind_param("is", $canje_info['puntos_usados'], $canje_info['id_usuario']);
                    if (!$stmt_return_points->execute()) throw new Exception("Error al ejecutar devolución puntos: " . $stmt_return_points->error);
                    $stmt_return_points->close();

                    // Devolver stock al premio (si el premio tiene stock)
                    $stmt_return_stock = $conn->prepare("UPDATE premios SET stock_disponible = stock_disponible + 1 WHERE id_premio = ? AND stock_disponible IS NOT NULL");
                    if (!$stmt_return_stock) throw new Exception("Error al preparar devolución stock: " . $conn->error);
                    $stmt_return_stock->bind_param("i", $canje_info['id_premio']);
                    if (!$stmt_return_stock->execute()) throw new Exception("Error al ejecutar devolución stock: " . $stmt_return_stock->error);
                    $stmt_return_stock->close();
                }
            }

            // Registrar en auditoría
            $log_accion = "CANJE GESTIONADO: " . strtoupper($nuevo_estado_canje);
            $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $detalles = "Canje ID: {$id_canje}. Nuevo estado: {$nuevo_estado_canje}. Observaciones: {$observaciones_admin_canje}";
            $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
            $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'canjes_usuario', $id_canje, $detalles, $ip_origen_auditoria);
            $stmt_audit->execute();
            $stmt_audit->close();

            $conn->commit();
            $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'success', 'texto' => "Canje #{$id_canje} actualizado a '{$nuevo_estado_canje}'."];

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Error al procesar el canje #{$id_canje}: " . $e->getMessage()];
            error_log("Error en gestión de canje: " . $e->getMessage());
        }
    } else {
        $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => "Datos de canje incompletos o inválidos."];
    }
    header("Location: admin_premios_flujo.php");
    exit();
}


// Obtener lista de premios
$premios = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $stmt_premios = $conn->prepare("SELECT id_premio, nombre_premio, descripcion_premio, puntos_requeridos, stock_disponible, activo, ruta_imagen_premio FROM premios ORDER BY activo DESC, nombre_premio ASC");
    if ($stmt_premios) {
        $stmt_premios->execute();
        $result = $stmt_premios->get_result();
        while ($row = $result->fetch_assoc()) {
            $premios[] = $row;
        }
        $stmt_premios->close();
    } else {
        error_log("Error al obtener premios: " . $conn->error);
    }

    // Obtener lista de canjes
    $canjes = [];
    $stmt_canjes = $conn->prepare("SELECT
                                    cu.id_canje,
                                    cu.id_usuario,
                                    u.nombre AS usuario_nombre,
                                    u.apellido AS usuario_apellido,
                                    p.nombre_premio,
                                    cu.puntos_usados,
                                    cu.codigo_canje_fisico,
                                    cu.fecha_canje,
                                    cu.estado_canje,
                                    cu.observaciones_admin
                                FROM
                                    canjes_usuario cu
                                JOIN
                                    usuarios u ON cu.id_usuario = u.id_usuario
                                JOIN
                                    premios p ON cu.id_premio = p.id_premio
                                ORDER BY
                                    FIELD(cu.estado_canje, 'pendiente_entrega', 'entregado', 'cancelado'),
                                    cu.fecha_canje DESC");
    if ($stmt_canjes) {
        $stmt_canjes->execute();
        $result_canjes = $stmt_canjes->get_result();
        while ($row_canje = $result_canjes->fetch_assoc()) {
            $canjes[] = $row_canje;
        }
        $stmt_canjes->close();
    } else {
        error_log("Error al obtener canjes: " . $conn->error);
    }
} else {
    $_SESSION['mensaje_accion_premio_admin'] = ['tipo' => 'error', 'texto' => 'Error de conexión a la base de datos. No se pueden cargar los datos.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Gestión de Premios</h3>
    <button class="btn btn-primary" onclick="abrirModalPremio('add')"><i class="fas fa-plus-circle"></i> Añadir Nuevo Premio</button>
    <?php if (!empty($premios)): ?>
        <div class="table-responsive-wrapper" style="margin-top:20px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Puntos</th>
                        <th>Stock</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($premios as $premio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($premio['id_premio']); ?></td>
                            <td>
                                <?php if (!empty($premio['ruta_imagen_premio'])): ?>
                                    <img src="<?php echo htmlspecialchars($premio['ruta_imagen_premio']); ?>" alt="<?php echo htmlspecialchars($premio['nombre_premio']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($premio['nombre_premio']); ?></td>
                            <td title="<?php echo htmlspecialchars($premio['descripcion_premio']); ?>">
                                <?php echo htmlspecialchars(substr($premio['descripcion_premio'] ?? '', 0, 50)) . (strlen($premio['descripcion_premio'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td><?php echo htmlspecialchars($premio['puntos_requeridos']); ?></td>
                            <td><?php echo ($premio['stock_disponible'] !== null) ? htmlspecialchars($premio['stock_disponible']) : 'Ilimitado'; ?></td>
                            <td>
                                <?php if ($premio['activo']): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalPremio('edit', <?php echo htmlspecialchars(json_encode($premio)); ?>)"><i class="fas fa-edit"></i> Editar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay premios configurados.</p>
    <?php endif; ?>
</div>

<div class="card" style="margin-top:30px;">
    <h3>Gestión de Canjes de Premios</h3>
    <?php if (!empty($canjes)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Canje</th>
                        <th>Usuario</th>
                        <th>Premio Canjeado</th>
                        <th>Puntos Usados</th>
                        <th>Código Canje</th>
                        <th>Fecha Canje</th>
                        <th>Estado</th>
                        <th>Observaciones Admin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($canjes as $canje): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($canje['id_canje']); ?></td>
                            <td><?php echo htmlspecialchars($canje['usuario_nombre'] . ' ' . $canje['usuario_apellido']); ?></td>
                            <td><?php echo htmlspecialchars($canje['nombre_premio']); ?></td>
                            <td><?php echo htmlspecialchars($canje['puntos_usados']); ?></td>
                            <td><strong><?php echo htmlspecialchars($canje['codigo_canje_fisico']); ?></strong></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($canje['fecha_canje'])); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    if ($canje['estado_canje'] === 'pendiente_entrega') echo 'warning';
                                    elseif ($canje['estado_canje'] === 'entregado') echo 'success';
                                    else echo 'danger'; // 'cancelado'
                                ?>"><?php echo htmlspecialchars($canje['estado_canje']); ?></span>
                            </td>
                            <td title="<?php echo htmlspecialchars($canje['observaciones_admin'] ?? 'Sin observaciones'); ?>">
                                <?php echo htmlspecialchars(substr($canje['observaciones_admin'] ?? 'Sin obs.', 0, 50)) . (strlen($canje['observaciones_admin'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td class="action-buttons">
                                <?php if ($canje['estado_canje'] === 'pendiente_entrega'): ?>
                                    <button class="btn-accion-modal" onclick="abrirModalGestionCanje(<?php echo $canje['id_canje']; ?>, '<?php echo htmlspecialchars(addslashes($canje['nombre_premio'])); ?>', 'entregado')"><i class="fas fa-check"></i> Marcar Entregado</button>
                                    <button class="btn-accion-modal" onclick="abrirModalGestionCanje(<?php echo $canje['id_canje']; ?>, '<?php echo htmlspecialchars(addslashes($canje['nombre_premio'])); ?>', 'cancelado')"><i class="fas fa-times"></i> Cancelar</button>
                                <?php else: ?>
                                    <small>Gestionado</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay canjes de premios registrados.</p>
    <?php endif; ?>
</div>


<div id="premioModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalPremio()">&times;</span>
        <h2 id="modalPremioTitle">Añadir/Editar Premio</h2>
        <form id="premioForm" method="POST" action="admin_premios_flujo.php" enctype="multipart/form-data">
            <input type="hidden" name="action_premio" id="action_premio">
            <input type="hidden" name="id_premio_edit" id="id_premio_edit">
            <input type="hidden" name="current_imagen_premio" id="current_imagen_premio">
            
            <div class="form-group">
                <label for="nombre_premio">Nombre del Premio:</label>
                <input type="text" id="nombre_premio" name="nombre_premio" required>
            </div>
            <div class="form-group">
                <label for="descripcion_premio">Descripción:</label>
                <textarea id="descripcion_premio" name="descripcion_premio" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="puntos_requeridos">Puntos Requeridos:</label>
                <input type="number" id="puntos_requeridos" name="puntos_requeridos" min="1" required>
            </div>
            <div class="form-group">
                <label for="stock_disponible">Stock Disponible (dejar vacío para ilimitado):</label>
                <input type="number" id="stock_disponible" name="stock_disponible" min="0">
            </div>
            <div class="form-group">
                <label for="imagen_premio">Imagen del Premio (JPG, PNG, GIF):</label>
                <input type="file" id="imagen_premio" name="imagen_premio" accept=".jpg, .jpeg, .png, .gif">
                <small id="current_image_preview" style="display:block; margin-top:10px;"></small>
            </div>
            <div class="form-group">
                <input type="checkbox" id="activo" name="activo" value="1">
                <label for="activo" style="display:inline-block; margin-left: 5px;">Activo</label>
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitPremioButton">Guardar Premio</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalPremio()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<div id="canjeModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalGestionCanje()">&times;</span>
        <h2 id="modalCanjeTitle">Gestionar Canje</h2>
        <form id="canjeForm" method="POST" action="admin_premios_flujo.php">
            <input type="hidden" name="id_canje_gestion" id="modal_id_canje_gestion">
            <input type="hidden" name="nuevo_estado_canje" id="modal_nuevo_estado_canje">
            
            <p>Estás a punto de <strong id="modalAccionCanjeTexto"></strong> el canje para el premio: "<strong id="modalNombrePremioCanje"></strong>".</p>

            <div class="form-group">
                <label for="observaciones_admin_canje">Observaciones del Administrador (Opcional):</label>
                <textarea id="observaciones_admin_canje" name="observaciones_admin_canje" rows="3" placeholder="Ej: Premio entregado. O, Razón de la cancelación..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitCanjeButton">Confirmar Acción</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalGestionCanje()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModalPremio(action, premioData = {}) {
    document.getElementById('action_premio').value = action;
    document.getElementById('modalPremioTitle').textContent = action === 'add' ? 'Añadir Nuevo Premio' : 'Editar Premio';
    document.getElementById('submitPremioButton').textContent = action === 'add' ? 'Guardar Premio' : 'Actualizar Premio';
    document.getElementById('premioForm').reset(); // Limpiar formulario

    document.getElementById('current_image_preview').innerHTML = '';
    document.getElementById('current_imagen_premio').value = '';

    if (action === 'edit') {
        document.getElementById('id_premio_edit').value = premioData.id_premio;
        document.getElementById('nombre_premio').value = premioData.nombre_premio;
        document.getElementById('descripcion_premio').value = premioData.descripcion_premio;
        document.getElementById('puntos_requeridos').value = premioData.puntos_requeridos;
        document.getElementById('stock_disponible').value = premioData.stock_disponible;
        document.getElementById('activo').checked = premioData.activo == 1;

        if (premioData.ruta_imagen_premio) {
            document.getElementById('current_imagen_premio').value = premioData.ruta_imagen_premio;
            document.getElementById('current_image_preview').innerHTML = 'Imagen actual: <img src="' + premioData.ruta_imagen_premio + '" style="width:50px;height:50px;vertical-align:middle;">';
        }
    }
    document.getElementById('premioModal').style.display = 'flex';
}

function cerrarModalPremio() {
    document.getElementById('premioModal').style.display = 'none';
}

function abrirModalGestionCanje(idCanje, nombrePremio, nuevoEstado) {
    document.getElementById('modal_id_canje_gestion').value = idCanje;
    document.getElementById('modal_nuevo_estado_canje').value = nuevoEstado;
    document.getElementById('modalNombrePremioCanje').textContent = nombrePremio;

    const modalAccionCanjeTexto = document.getElementById('modalAccionCanjeTexto');
    const submitCanjeButton = document.getElementById('submitCanjeButton');

    if (nuevoEstado === 'entregado') {
        modalAccionCanjeTexto.textContent = "MARCAR COMO ENTREGADO";
        submitCanjeButton.className = 'btn btn-success';
        submitCanjeButton.textContent = 'Confirmar Entrega';
    } else if (nuevoEstado === 'cancelado') {
        modalAccionCanjeTexto.textContent = "CANCELAR";
        submitCanjeButton.className = 'btn btn-danger';
        submitCanjeButton.textContent = 'Confirmar Cancelación';
    }
    document.getElementById('canjeModal').style.display = 'flex';
}

function cerrarModalGestionCanje() {
    document.getElementById('canjeModal').style.display = 'none';
    document.getElementById('observaciones_admin_canje').value = '';
}

window.onclick = function(event) {
    const premioModal = document.getElementById('premioModal');
    const canjeModal = document.getElementById('canjeModal');
    if (event.target == premioModal) {
        cerrarModalPremio();
    }
    if (event.target == canjeModal) {
        cerrarModalGestionCanje();
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