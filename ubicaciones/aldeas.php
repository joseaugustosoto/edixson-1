<?php
require_once '../includes/auth.php';
require_login(); // Protege esta página
require_once '../includes/db.php'; // Conexión PDO disponible como $pdo

// Lógica PHP para obtener las aldeas (con sus ubicaciones y estado)
try {
    $sql = "SELECT
                a.aldea_id, a.nombre_aldea, a.direccion, a.estado AS estado_aldea,
                p.nombre_parroquia, m.nombre_municipio, e.nombre_estado
            FROM aldea a
            JOIN parroquia p ON a.parroquia_id = p.parroquia_id
            JOIN municipio m ON p.municipio_id = m.municipio_id
            JOIN estado e ON m.estado_id = e.estado_id
            ORDER BY e.nombre_estado, m.nombre_municipio, p.nombre_parroquia, a.nombre_aldea";

    $stmt = $pdo->query($sql);
    $aldeas = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "Error al cargar aldeas: " . $e->getMessage();
    $aldeas = []; // Asegurar que $aldeas sea un array vacío en caso de error
}

// Lógica PHP para manejar acciones (activar, desactivar, eliminar) - Esto iría ANTES del HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $aldea_id = $_POST['aldea_id'] ?? null;
    if ($aldea_id) {
        try {
            if ($_POST['action'] === 'toggle_status') {
                $current_status = $_POST['current_status'] ?? 'Activa';
                $new_status = ($current_status === 'Activa') ? 'Inactiva' : 'Activa';
                $sql_update = "UPDATE aldea SET estado = :new_status WHERE aldea_id = :aldea_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindParam(':new_status', $new_status);
                $stmt_update->bindParam(':aldea_id', $aldea_id);
                $stmt_update->execute();
                // Redirigir para refrescar la lista y evitar reenvío del POST
                header('Location: aldeas.php?success=status_updated');
                exit();

            } elseif ($_POST['action'] === 'delete') {
                 // Aquí va la lógica para eliminar (cuidado con ON DELETE RESTRICT si aplica)
                 $sql_delete = "DELETE FROM aldea WHERE aldea_id = :aldea_id";
                 $stmt_delete = $pdo->prepare($sql_delete);
                 $stmt_delete->bindParam(':aldea_id', $aldea_id);
                 $stmt_delete->execute();
                 header('Location: aldeas.php?success=aldea_deleted');
                 exit();
            }
        } catch (PDOException $e) {
             // Manejar errores, especialmente si ON DELETE RESTRICT bloquea la eliminación
             $error_message = "Error al procesar acción: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Aldeas - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <h1>Gestión de Aldeas</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Operación realizada con éxito.</p>
    <?php endif; ?>

    <p><a href="agregar_aldea.php" class="btn btn-primary">Registrar Nueva Aldea</a></p>

    <table>
        <thead>
            <tr>
                <th>Nombre Aldea</th>
                <th>Dirección</th>
                <th>Parroquia</th>
                <th>Municipio</th>
                <th>Estado</th>
                <th>Estado (Activa/Inactiva)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aldeas)): ?>
                <tr>
                    <td colspan="7">No hay aldeas registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($aldeas as $aldea): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aldea['nombre_aldea']); ?></td>
                        <td><?php echo htmlspecialchars($aldea['direccion'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($aldea['nombre_parroquia']); ?></td>
                        <td><?php echo htmlspecialchars($aldea['nombre_municipio']); ?></td>
                        <td><?php echo htmlspecialchars($aldea['nombre_estado']); ?></td>
                        <td>
                             <span class="status-<?php echo strtolower($aldea['estado_aldea']); ?>">
                                <?php echo htmlspecialchars($aldea['estado_aldea']); ?>
                             </span>
                        </td>
                        <td>
                            <a href="editar_aldea.php?id=<?php echo $aldea['aldea_id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="gestionar_aldea_pnf.php?aldea_id=<?php echo $aldea['aldea_id']; ?>" class="btn btn-info">Ver PNF</a>

                            <form action="aldeas.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="aldea_id" value="<?php echo $aldea['aldea_id']; ?>">
                                <input type="hidden" name="current_status" value="<?php echo $aldea['estado_aldea']; ?>">
                                <input type="hidden" name="action" value="toggle_status">
                                <button type="submit" class="btn btn-toggle-status">
                                    <?php echo ($aldea['estado_aldea'] === 'Activa') ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                            <form action="aldeas.php" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Está seguro de eliminar esta Aldea?');">
                                <input type="hidden" name="aldea_id" value="<?php echo $aldea['aldea_id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>