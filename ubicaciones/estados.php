<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$error_message = '';
$success_message = '';

// Procesar acciones (Agregar, Editar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nombre_estado = $_POST['nombre_estado'] ?? '';
    $estado_id = $_POST['estado_id'] ?? null;

    try {
        if ($action === 'add' && !empty($nombre_estado)) {
            $sql = "INSERT INTO estado (nombre_estado) VALUES (:nombre_estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_estado', $nombre_estado);
            $stmt->execute();
            $success_message = 'Estado agregado con éxito.';
        } elseif ($action === 'edit' && !empty($nombre_estado) && $estado_id) {
            $sql = "UPDATE estado SET nombre_estado = :nombre_estado WHERE estado_id = :estado_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_estado', $nombre_estado);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->execute();
            $success_message = 'Estado actualizado con éxito.';
        } elseif ($action === 'delete' && $estado_id) {
            $sql = "DELETE FROM estado WHERE estado_id = :estado_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->execute();
            $success_message = 'Estado eliminado con éxito.';
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Obtener lista de estados
try {
    $sql = "SELECT * FROM estado ORDER BY nombre_estado";
    $stmt = $pdo->query($sql);
    $estados = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar los estados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estados</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <div class="container">
        <h1>Gestionar Estados</h1>

        <!-- Mensajes de éxito o error -->
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para agregar un nuevo estado -->
        <div class="form-container">
            <h2>Agregar Nuevo Estado</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add">
                <label for="nombre_estado">Nombre del Estado:</label>
                <input type="text" id="nombre_estado" name="nombre_estado" placeholder="Ingrese el nombre del estado" required>
                <button type="submit" class="btn btn-primary">Agregar Estado</button>
            </form>
        </div>

        <!-- Tabla de estados -->
        <h2>Lista de Estados</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estados as $estado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($estado['nombre_estado']); ?></td>
                        <td>
                            <!-- Formulario para editar -->
                            <form action="" method="POST" class="inline-form">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="estado_id" value="<?php echo $estado['estado_id']; ?>">
                                <input type="text" name="nombre_estado" value="<?php echo htmlspecialchars($estado['nombre_estado']); ?>" required>
                                <button type="submit" class="btn btn-edit">Editar</button>
                            </form>
                            <!-- Formulario para eliminar -->
                            <form action="" method="POST" class="inline-form" onsubmit="return confirm('¿Está seguro de eliminar este estado?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="estado_id" value="<?php echo $estado['estado_id']; ?>">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>