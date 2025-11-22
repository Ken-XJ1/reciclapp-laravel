<?php
// admin_logros_flujo.php
$titulo_pagina = "Gestión de Logros";
$pagina_actual = "admin_logros_flujo.php"; // Para marcar activo en el sidebar
include 'admin_layout.php'; // Incluye el layout base del administrador
include 'conexion.php';     // Conecta a reciclapp_flujo_db

$mensaje_accion = $_SESSION['mensaje_accion_logro_admin'] ?? null;
unset($_SESSION['mensaje_accion_logro_admin']);

// Lógica para AGREGAR/EDITAR logro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action_logro']) && ($_POST['action_logro'] === 'add' || $_POST['action_logro'] === 'edit'))) {
    $id_logro = (int)($_POST['id_logro_edit'] ?? 0);
    $nombre_logro = $_POST['nombre_logro'] ?? null;
    $descripcion_logro = $_POST['descripcion_logro'] ?? null;
    $tipo_criterio = $_POST['tipo_criterio'] ?? null;
    $valor_criterio = (int)($_POST['valor_criterio'] ?? 0);
    $puntos_recompensa = (int)($_POST['puntos_recompensa'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Manejo de la imagen de logro
    $ruta_icono = '';
    $upload_dir = 'media/logros/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['icono_logro']) && $_FILES['icono_logro']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['icono_logro']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['icono_logro']['name']);
        $destination = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            $ruta_icono = $destination;
        } else {
            $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'error', 'texto' => "Error al subir el icono."];
            header("Location: admin_logros_flujo.php");
            exit();
        }
    } else if ($_POST['action_logro'] === 'edit' && isset($_POST['current_icono_logro'])) {
        $ruta_icono = $_POST['current_icono_logro']; // Mantener el icono actual si no se sube uno nuevo
    }

    if (empty($nombre_logro) || empty($tipo_criterio) || $valor_criterio <= 0) {
        $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'error', 'texto' => "Nombre, tipo y valor de criterio son obligatorios para el logro."];
    } else {
        $conn->begin_transaction();
        try {
            if ($_POST['action_logro'] === 'add') {
                $stmt = $conn->prepare("INSERT INTO logros (nombre_logro, descripcion_logro, tipo_criterio, valor_criterio, puntos_recompensa, activo, ruta_icono) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) throw new Exception("Error al preparar ADD logro: " . $conn->error);
                $stmt->bind_param("sssiiss", $nombre_logro, $descripcion_logro, $tipo_criterio, $valor_criterio, $puntos_recompensa, $activo, $ruta_icono);
                if (!$stmt->execute()) throw new Exception("Error al ejecutar ADD logro: " . $stmt->error);
                $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'success', 'texto' => "Logro '{$nombre_logro}' agregado con éxito."];
                $log_accion = "LOGRO AGREGADO";
                $id_registro_modificado = $conn->insert_id;
            } elseif ($_POST['action_logro'] === 'edit') {
                $sql = "UPDATE logros SET nombre_logro = ?, descripcion_logro = ?, tipo_criterio = ?, valor_criterio = ?, puntos_recompensa = ?, activo = ?";
                if (!empty($ruta_icono)) {
                    $sql .= ", ruta_icono = ?";
                }
                $sql .= " WHERE id_logro = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new Exception("Error al preparar EDIT logro: " . $conn->error);

                if (!empty($ruta_icono)) {
                    $stmt->bind_param("sssiisii", $nombre_logro, $descripcion_logro, $tipo_criterio, $valor_criterio, $puntos_recompensa, $activo, $ruta_icono, $id_logro);
                } else {
                    $stmt->bind_param("sssiisi", $nombre_logro, $descripcion_logro, $tipo_criterio, $valor_criterio, $puntos_recompensa, $activo, $id_logro);
                }
                
                if (!$stmt->execute()) throw new Exception("Error al ejecutar EDIT logro: " . $stmt->error);
                $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'success', 'texto' => "Logro '{$nombre_logro}' actualizado con éxito."];
                $log_accion = "LOGRO EDITADO";
                $id_registro_modificado = $id_logro;
            }
            $stmt->close();

            // Registrar en auditoría
            $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $detalles = "Logro: {$nombre_logro}. Criterio: {$tipo_criterio} {$valor_criterio}. Puntos: {$puntos_recompensa}. Activo: {$activo}. Icono: {$ruta_icono}";
            $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
            $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'logros', $id_registro_modificado, $detalles, $ip_origen_auditoria);
            $stmt_audit->execute();
            $stmt_audit->close();
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'error', 'texto' => "Error en la operación del logro: " . $e->getMessage()];
            error_log("Error en gestión de logros (add/edit): " . $e->getMessage());
        }
    }
    header("Location: admin_logros_flujo.php");
    exit();
}

// Obtener lista de logros
$logros = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $stmt_logros = $conn->prepare("SELECT id_logro, nombre_logro, descripcion_logro, tipo_criterio, valor_criterio, puntos_recompensa, activo, ruta_icono FROM logros ORDER BY activo DESC, nombre_logro ASC");
    if ($stmt_logros) {
        $stmt_logros->execute();
        $result = $stmt_logros->get_result();
        while ($row = $result->fetch_assoc()) {
            $logros[] = $row;
        }
        $stmt_logros->close();
    } else {
        error_log("Error al obtener logros: " . $conn->error);
    }
} else {
    $_SESSION['mensaje_accion_logro_admin'] = ['tipo' => 'error', 'texto' => 'Error crítico de conexión a la base de datos. No se pueden cargar los logros.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Gestión de Logros</h3>
    <button class="btn btn-primary" onclick="abrirModalLogro('add')"><i class="fas fa-plus-circle"></i> Añadir Nuevo Logro</button>
    <?php if (!empty($logros)): ?>
        <div class="table-responsive-wrapper" style="margin-top:20px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Icono</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Tipo Criterio</th>
                        <th>Valor Criterio</th>
                        <th>Puntos Recompensa</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logros as $logro): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($logro['id_logro']); ?></td>
                            <td>
                                <?php if (!empty($logro['ruta_icono'])): ?>
                                    <img src="<?php echo htmlspecialchars($logro['ruta_icono']); ?>" alt="<?php echo htmlspecialchars($logro['nombre_logro']); ?>" style="width: 40px; height: 40px; object-fit: contain; border-radius: 5px;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($logro['nombre_logro']); ?></td>
                            <td title="<?php echo htmlspecialchars($logro['descripcion_logro']); ?>">
                                <?php echo htmlspecialchars(substr($logro['descripcion_logro'] ?? '', 0, 50)) . (strlen($logro['descripcion_logro'] ?? '') > 50 ? '...' : ''); ?>
                            </td>
                            <td><?php echo htmlspecialchars($logro['tipo_criterio']); ?></td>
                            <td><?php echo htmlspecialchars($logro['valor_criterio']); ?></td>
                            <td><?php echo htmlspecialchars($logro['puntos_recompensa']); ?></td>
                            <td>
                                <?php if ($logro['activo']): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalLogro('edit', <?php echo htmlspecialchars(json_encode($logro)); ?>)"><i class="fas fa-edit"></i> Editar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay logros configurados.</p>
    <?php endif; ?>
</div>

<div id="logroModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalLogro()">&times;</span>
        <h2 id="modalLogroTitle">Añadir/Editar Logro</h2>
        <form id="logroForm" method="POST" action="admin_logros_flujo.php" enctype="multipart/form-data">
            <input type="hidden" name="action_logro" id="action_logro">
            <input type="hidden" name="id_logro_edit" id="id_logro_edit">
            <input type="hidden" name="current_icono_logro" id="current_icono_logro">
            
            <div class="form-group">
                <label for="nombre_logro">Nombre del Logro:</label>
                <input type="text" id="nombre_logro" name="nombre_logro" required>
            </div>
            <div class="form-group">
                <label for="descripcion_logro">Descripción:</label>
                <textarea id="descripcion_logro" name="descripcion_logro" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="tipo_criterio">Tipo de Criterio:</label>
                <select id="tipo_criterio" name="tipo_criterio" required>
                    <option value="">Selecciona un tipo</option>
                    <option value="puntos_acumulados">Puntos Acumulados</option>
                    <option value="recolecciones_completadas">Recolecciones Completadas</option>
                    <option value="puntos_propuestos_aprobados">Puntos Propuestos Aprobados</option>
                    </select>
            </div>
            <div class="form-group">
                <label for="valor_criterio">Valor del Criterio (ej. 1000 puntos, 5 recolecciones):</label>
                <input type="number" id="valor_criterio" name="valor_criterio" min="1" required>
            </div>
            <div class="form-group">
                <label for="puntos_recompensa">Puntos de Recompensa (por desbloquear este logro):</label>
                <input type="number" id="puntos_recompensa" name="puntos_recompensa" min="0" required>
            </div>
            <div class="form-group">
                <label for="icono_logro">Icono del Logro (JPG, PNG, GIF):</label>
                <input type="file" id="icono_logro" name="icono_logro" accept=".jpg, .jpeg, .png, .gif">
                <small id="current_icono_preview" style="display:block; margin-top:10px;"></small>
            </div>
            <div class="form-group">
                <input type="checkbox" id="activo" name="activo" value="1">
                <label for="activo" style="display:inline-block; margin-left: 5px;">Activo</label>
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitLogroButton">Guardar Logro</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalLogro()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModalLogro(action, logroData = {}) {
    document.getElementById('action_logro').value = action;
    document.getElementById('modalLogroTitle').textContent = action === 'add' ? 'Añadir Nuevo Logro' : 'Editar Logro';
    document.getElementById('submitLogroButton').textContent = action === 'add' ? 'Guardar Logro' : 'Actualizar Logro';
    document.getElementById('logroForm').reset(); // Limpiar formulario

    document.getElementById('current_icono_preview').innerHTML = '';
    document.getElementById('current_icono_logro').value = '';

    if (action === 'edit') {
        document.getElementById('id_logro_edit').value = logroData.id_logro;
        document.getElementById('nombre_logro').value = logroData.nombre_logro;
        document.getElementById('descripcion_logro').value = logroData.descripcion_logro;
        document.getElementById('tipo_criterio').value = logroData.tipo_criterio;
        document.getElementById('valor_criterio').value = logroData.valor_criterio;
        document.getElementById('puntos_recompensa').value = logroData.puntos_recompensa;
        document.getElementById('activo').checked = logroData.activo == 1;

        if (logroData.ruta_icono) {
            document.getElementById('current_icono_logro').value = logroData.ruta_icono;
            document.getElementById('current_icono_preview').innerHTML = 'Icono actual: <img src="' + logroData.ruta_icono + '" style="width:40px;height:40px;vertical-align:middle;">';
        }
    }
    document.getElementById('logroModal').style.display = 'flex';
}

function cerrarModalLogro() {
    document.getElementById('logroModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('logroModal');
    if (event.target == modal) {
        cerrarModalLogro();
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