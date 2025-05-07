<?php
session_start(); // Inicia la sesión al principio de la página

// Incluir el archivo de conexión a BD
require_once '../includes/db.php'; // Asume que este archivo existe y conecta a $pdo
require_once '../includes/auth.php'; // Incluir autenticación
require_login(); // Asegura que el usuario esté logueado

// Lógica PHP para obtener los PNF (Programas Nacionales de Formación)
try {
    $sql = "SELECT p.pnf_id, p.codigo_pnf, p.nombre_pnf, p.estado,
                   GROUP_CONCAT(a.nombre_aldea SEPARATOR ', ') AS aldeas
            FROM pnf p
            LEFT JOIN aldea_pnf ap ON p.pnf_id = ap.pnf_id
            LEFT JOIN aldea a ON ap.aldea_id = a.aldea_id
            GROUP BY p.pnf_id
            ORDER BY p.nombre_pnf";
    $stmt = $pdo->query($sql);
    $pnfs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar PNF: " . $e->getMessage();
    $pnfs = []; // Asegurar que $pnfs sea un array vacío en caso de error
}

// Lógica PHP para manejar acciones (activar, desactivar, eliminar) - Esto iría ANTES del HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pnf_id = $_POST['pnf_id'] ?? null;
    if ($pnf_id) {
        try {
            if ($_POST['action'] === 'toggle_status') {
                $current_status = $_POST['current_status'] ?? 'Activo';
                $new_status = ($current_status === 'Activo') ? 'Inactivo' : 'Activo';
                $sql_update = "UPDATE pnf SET estado = :new_status WHERE pnf_id = :pnf_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindParam(':new_status', $new_status);
                $stmt_update->bindParam(':pnf_id', $pnf_id);
                $stmt_update->execute();
                header('Location: pnf.php?success=status_updated');
                exit();
            } elseif ($_POST['action'] === 'delete') {
                $sql_delete = "DELETE FROM pnf WHERE pnf_id = :pnf_id";
                $stmt_delete = $pdo->prepare($sql_delete);
                $stmt_delete->bindParam(':pnf_id', $pnf_id);
                $stmt_delete->execute();
                header('Location: pnf.php?success=pnf_deleted');
                exit();
            }
        } catch (PDOException $e) {
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
    <title>Gestionar PNF - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>
    <h1>Gestión de PNF</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Operación realizada con éxito.</p>
    <?php endif; ?>

    <p><a href="agregar_pnf.php" class="btn btn-primary">Registrar Nuevo PNF</a></p>

    <table>
        <thead>
            <tr>
                <th>Código PNF</th>
                <th>Nombre PNF</th>
                <th>Aldeas</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pnfs as $pnf): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pnf['codigo_pnf']); ?></td>
                    <td><?php echo htmlspecialchars($pnf['nombre_pnf']); ?></td>
                    <td><?php echo htmlspecialchars($pnf['aldeas']); ?></td>
                    <td><?php echo htmlspecialchars($pnf['estado']); ?></td>
                    <td>
                        <a href="editar_pnf.php?id=<?php echo $pnf['pnf_id']; ?>" class="btn btn-edit">Editar</a>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="pnf_id" value="<?php echo $pnf['pnf_id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $pnf['estado']; ?>">
                            <button type="submit" name="action" value="toggle_status" class="btn btn-toggle">
                                <?php echo ($pnf['estado'] === 'Activo') ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="pnf_id" value="<?php echo $pnf['pnf_id']; ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este PNF?');">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>