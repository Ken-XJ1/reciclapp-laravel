<?php
$titulo_pagina = "Gestión de Usuarios";
$pagina_actual = "admin_gestion_usuarios.php"; 
include 'admin_layout.php'; 
include 'conexion.php';    

$mensaje_accion = $_SESSION['mensaje_accion_usuario'] ?? null;
unset($_SESSION['mensaje_accion_usuario']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario_gestion'])) {
    $id_usuario = $_POST['id_usuario_gestion'];
    $accion = $_POST['accion_usuario'] ?? '';
    $valor_nuevo = $_POST['valor_nuevo'] ?? ''; 

    if ($id_usuario && !empty($accion) && !empty($valor_nuevo)) {
        $conn->begin_transaction();
        try {
            if ($accion === 'cambiar_estado') {
                if (!in_array($valor_nuevo, ['activo', 'inactivo', 'bloqueado'])) {
                    throw new Exception("Estado de usuario no válido.");
                }
                $stmt = $conn->prepare("UPDATE usuarios SET estado = ?, fecha_ultima_actualizacion = NOW() WHERE id_usuario = ?");
                $stmt->bind_param("ss", $valor_nuevo, $id_usuario);
                $log_accion = "ESTADO USUARIO MODIFICADO";
                $_SESSION['mensaje_accion_usuario'] = ['tipo' => 'success', 'texto' => "Estado del usuario '{$id_usuario}' cambiado a '{$valor_nuevo}'."];
            } elseif ($accion === 'cambiar_rol') {
                if (!in_array($valor_nuevo, ['usuario', 'administrador'])) {
                    throw new Exception("Rol de usuario no válido.");
                }
                $stmt = $conn->prepare("UPDATE usuarios SET rol = ?, fecha_ultima_actualizacion = NOW() WHERE id_usuario = ?");
                $stmt->bind_param("ss", $valor_nuevo, $id_usuario);
                $log_accion = "ROL USUARIO MODIFICADO";
                $_SESSION['mensaje_accion_usuario'] = ['tipo' => 'success', 'texto' => "Rol del usuario '{$id_usuario}' cambiado a '{$valor_nuevo}'."];
            } else {
                throw new Exception("Acción no válida.");
            }

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la actualización: " . $stmt->error);
            }
            $stmt->close();

            $stmt_audit = $conn->prepare("INSERT INTO auditoria (id_usuario_afectado, accion_realizada, tabla_modificada, id_registro_modificado, detalles_accion, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $detalles = "Usuario ID: {$id_usuario}. Acción: {$accion}. Valor: {$valor_nuevo}.";
            $ip_origen_auditoria = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
            $stmt_audit->bind_param("ssssss", $_SESSION['user_id'], $log_accion, 'usuarios', $id_usuario, $detalles, $ip_origen_auditoria);
            $stmt_audit->execute();
            $stmt_audit->close();

            $conn->commit(); 
        } catch (Exception $e) {
            $conn->rollback(); 
            $_SESSION['mensaje_accion_usuario'] = ['tipo' => 'error', 'texto' => "Error al procesar la acción para el usuario '{$id_usuario}': " . $e->getMessage()];
            error_log("Error en gestión de usuario: " . $e->getMessage());
        }
    } else {
        $_SESSION['mensaje_accion_usuario'] = ['tipo' => 'error', 'texto' => "Datos de acción incompletos o inválidos."];
    }
    header("Location: admin_gestion_usuarios.php"); 
    exit();
}


// Obtener lista de usuarios
$usuarios = [];
$stmt_usuarios = $conn->prepare("SELECT id_usuario, nombre, apellido, email, rol, estado, puntos_acumulados, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
if ($stmt_usuarios) {
    $stmt_usuarios->execute();
    $result = $stmt_usuarios->get_result();
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    $stmt_usuarios->close();
} else {
    error_log("Error al obtener usuarios: " . $conn->error);
    $mensaje_accion = ['tipo' => 'error', 'texto' => 'Error al cargar la lista de usuarios.'];
}

?>

<?php if ($mensaje_accion): ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensaje_accion['tipo']); ?>">
        <?php echo htmlspecialchars($mensaje_accion['texto']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Gestión de Usuarios Registrados</h3>
    <?php if (!empty($usuarios)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Puntos</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    if ($usuario['rol'] === 'administrador') echo 'info';
                                    else echo 'secondary';
                                ?>"><?php echo htmlspecialchars($usuario['rol']); ?></span>
                            </td>
                            <td>
                                <span class="badge badge-<?php
                                    if ($usuario['estado'] === 'activo') echo 'success';
                                    elseif ($usuario['estado'] === 'inactivo') echo 'danger';
                                    else echo 'warning'; 
                                ?>"><?php echo htmlspecialchars($usuario['estado']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['puntos_acumulados']); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($usuario['fecha_registro'])); ?></td>
                            <td class="action-buttons">
                                <button class="btn-accion-modal" onclick="abrirModalGestionUsuario('<?php echo htmlspecialchars(addslashes($usuario['id_usuario'])); ?>', 'cambiar_estado', '<?php echo htmlspecialchars(addslashes($usuario['nombre'] . ' ' . $usuario['apellido'])); ?>', '<?php echo htmlspecialchars($usuario['estado']); ?>')"><i class="fas fa-toggle-on"></i> Estado</button>
                                <button class="btn-accion-modal" onclick="abrirModalGestionUsuario('<?php echo htmlspecialchars(addslashes($usuario['id_usuario'])); ?>', 'cambiar_rol', '<?php echo htmlspecialchars(addslashes($usuario['nombre'] . ' ' . $usuario['apellido'])); ?>', '<?php echo htmlspecialchars($usuario['rol']); ?>')"><i class="fas fa-user-tag"></i> Rol</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</div>

<div id="gestionUsuarioModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="cerrarModalGestionUsuario()">&times;</span>
        <h2 id="modalGestionUsuarioTitle">Gestionar Usuario</h2>
        <form id="gestionUsuarioForm" method="POST" action="admin_gestion_usuarios.php">
            <input type="hidden" name="id_usuario_gestion" id="modal_id_usuario_gestion">
            <input type="hidden" name="accion_usuario" id="modal_accion_usuario">
            
            <p>Estás gestionando al usuario: "<strong id="modalNombreUsuario"></strong>".</p>

            <div class="form-group" id="estadoGroup">
                <label for="valor_nuevo_estado">Cambiar Estado a:</label>
                <select id="valor_nuevo_estado" name="valor_nuevo" class="form-control">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="bloqueado">Bloqueado</option>
                </select>
            </div>

            <div class="form-group" id="rolGroup">
                <label for="valor_nuevo_rol">Cambiar Rol a:</label>
                <select id="valor_nuevo_rol" name="valor_nuevo" class="form-control">
                    <option value="usuario">Usuario</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" id="modalSubmitButtonGestion">Confirmar</button>
            <button type="button" class="btn btn-light" onclick="cerrarModalGestionUsuario()" style="margin-left:10px;">Cancelar</button>
        </form>
    </div>
</div>

<style>
.badge {
    display: inline-block;
    padding: .35em .65em;
    font-size: .75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25rem;
    color: #fff;
}
.badge-success { background-color: var(--admin-primary-color); }
.badge-danger { background-color: var(--admin-danger-color); }
.badge-warning { background-color: var(--admin-warning-color); color: var(--admin-dark-color); }
.badge-info { background-color: var(--admin-info-color); }
.badge-secondary { background-color: var(--admin-text-light); }
</style>

<script>
function abrirModalGestionUsuario(idUsuario, accion, nombreUsuario, valorActual) {
    document.getElementById('modal_id_usuario_gestion').value = idUsuario;
    document.getElementById('modal_accion_usuario').value = accion;
    document.getElementById('modalNombreUsuario').textContent = nombreUsuario;

    const estadoGroup = document.getElementById('estadoGroup');
    const rolGroup = document.getElementById('rolGroup');
    const selectEstado = document.getElementById('valor_nuevo_estado');
    const selectRol = document.getElementById('valor_nuevo_rol');
    const modalSubmitButton = document.getElementById('modalSubmitButtonGestion');

    estadoGroup.style.display = 'none';
    rolGroup.style.display = 'none';
    selectEstado.removeAttribute('required');
    selectRol.removeAttribute('required');

    if (accion === 'cambiar_estado') {
        estadoGroup.style.display = 'block';
        selectEstado.value = valorActual; 
        selectEstado.setAttribute('required', 'required');
        document.getElementById('modalGestionUsuarioTitle').textContent = "Cambiar Estado de Usuario";
        modalSubmitButton.className = 'btn btn-secondary';
        modalSubmitButton.textContent = 'Guardar Estado';
    } else if (accion === 'cambiar_rol') {
        rolGroup.style.display = 'block';
        selectRol.value = valorActual; 
        selectRol.setAttribute('required', 'required');
        document.getElementById('modalGestionUsuarioTitle').textContent = "Cambiar Rol de Usuario";
        modalSubmitButton.className = 'btn btn-info';
        modalSubmitButton.textContent = 'Guardar Rol';
    }
    document.getElementById('gestionUsuarioModal').style.display = 'flex';
}

function cerrarModalGestionUsuario() {
    document.getElementById('gestionUsuarioModal').style.display = 'none';
    document.getElementById('modal_id_usuario_gestion').value = '';
    document.getElementById('modal_accion_usuario').value = '';
    document.getElementById('valor_nuevo_estado').value = '';
    document.getElementById('valor_nuevo_rol').value = '';
}

window.onclick = function(event) {
    const modal = document.getElementById('gestionUsuarioModal');
    if (event.target == modal) {
        cerrarModalGestionUsuario();
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