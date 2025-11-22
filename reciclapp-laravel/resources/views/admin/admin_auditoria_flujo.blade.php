<?php
$titulo_pagina = "Auditoría del Sistema";
$pagina_actual = "admin_auditoria_flujo.php"; 
include 'admin_layout.php'; 
include 'conexion.php';   

$registros_auditoria = [];

if (isset($conn) && $conn && !$conn->connect_error) {

    $stmt_auditoria = $conn->prepare("SELECT
                                        a.id_registro_auditoria,
                                        a.fecha_hora,
                                        u_realiza.nombre AS realiza_nombre,
                                        u_realiza.apellido AS realiza_apellido,
                                        a.accion_realizada,
                                        a.tabla_modificada,
                                        a.id_registro_modificado,
                                        a.detalles_accion,
                                        a.ip_origen,
                                        u_afectado.nombre AS afectado_nombre,
                                        u_afectado.apellido AS afectado_apellido
                                    FROM
                                        auditoria a
                                    LEFT JOIN
                                        usuarios u_realiza ON a.id_usuario_afectado = u_realiza.id_usuario 
                                        usuarios u_afectado ON a.id_registro_modificado = u_afectado.id_usuario AND a.tabla_modificada = 'usuarios' 
                                        a.fecha_hora DESC
                                    LIMIT 100"); 
    if ($stmt_auditoria) {
        $stmt_auditoria->execute();
        $result = $stmt_auditoria->get_result();
        while ($row = $result->fetch_assoc()) {
            $registros_auditoria[] = $row;
        }
        $stmt_auditoria->close();
    } else {
        error_log("Error al obtener registros de auditoría: " . $conn->error);
    }
} else {
    $_SESSION['mensaje_accion_auditoria'] = ['tipo' => 'error', 'texto' => 'Error de conexión a la base de datos. No se pueden cargar los registros de auditoría.'];
}

?>

<?php if (isset($_SESSION['mensaje_accion_auditoria'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['mensaje_accion_auditoria']['tipo']); ?>">
        <?php echo htmlspecialchars($_SESSION['mensaje_accion_auditoria']['texto']); ?>
    </div>
    <?php unset($_SESSION['mensaje_accion_auditoria']); ?>
<?php endif; ?>

<div class="card">
    <h3>Registros de Auditoría del Sistema</h3>
    <p>Aquí se registran las acciones importantes realizadas por los administradores y eventos clave del sistema.</p>
    <?php if (!empty($registros_auditoria)): ?>
        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Reg.</th>
                        <th>Fecha y Hora</th>
                        <th>Realizado por</th>
                        <th>Acción Realizada</th>
                        <th>Tabla Modificada</th>
                        <th>ID Reg. Modif.</th>
                        <th>Usuario Afectado (Si aplica)</th>
                        <th>Detalles</th>
                        <th>IP Origen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros_auditoria as $registro): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($registro['id_registro_auditoria']); ?></td>
                            <td><?php echo date("d/m/Y H:i:s", strtotime($registro['fecha_hora'])); ?></td>
                            <td><?php echo htmlspecialchars($registro['realiza_nombre'] . ' ' . $registro['realiza_apellido'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($registro['accion_realizada']); ?></td>
                            <td><?php echo htmlspecialchars($registro['tabla_modificada'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($registro['id_registro_modificado'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($registro['afectado_nombre']): ?>
                                    <?php echo htmlspecialchars($registro['afectado_nombre'] . ' ' . $registro['afectado_apellido']); ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td title="<?php echo htmlspecialchars($registro['detalles_accion']); ?>">
                                <?php echo htmlspecialchars(substr($registro['detalles_accion'] ?? '', 0, 70)) . (strlen($registro['detalles_accion'] ?? '') > 70 ? '...' : ''); ?>
                            </td>
                            <td><?php echo htmlspecialchars($registro['ip_origen'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No se encontraron registros de auditoría.</p>
    <?php endif; ?>
</div>

<script>
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