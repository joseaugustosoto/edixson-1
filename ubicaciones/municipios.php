<?php
// filepath: e:\xampp\htdocs\edixson-1\ubicaciones\municipios.php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$error_message = '';
$success_message = '';

// Procesar acciones (Agregar, Editar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nombre_municipio = $_POST['nombre_municipio'] ?? '';
    $estado_id = $_POST['estado_id'] ?? null;
    $municipio_id = $_POST['municipio_id'] ?? null;

    try {
        if ($action === 'add' && !empty($nombre_municipio) && !empty($estado_id)) {
            $sql = "INSERT INTO municipio (nombre_municipio, estado_id) VALUES (:nombre_municipio, :estado_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_municipio', $nombre_municipio);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->execute();
            $success_message = 'Municipio agregado con éxito.';
        } elseif ($action === 'edit' && !empty($nombre_municipio) && $municipio_id) {
            $sql = "UPDATE municipio SET nombre_municipio = :nombre_municipio, estado_id = :estado_id WHERE municipio_id = :municipio_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_municipio', $nombre_municipio);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->bindParam(':municipio_id', $municipio_id);
            $stmt->execute();
            $success_message = 'Municipio actualizado con éxito.';
        } elseif ($action === 'delete' && $municipio_id) {
            $sql = "DELETE FROM municipio WHERE municipio_id = :municipio_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':municipio_id', $municipio_id);
            $stmt->execute();
            $success_message = 'Municipio eliminado con éxito.';
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Obtener lista de municipios
try {
    $sql = "SELECT m.*, e.nombre_estado FROM municipio m JOIN estado e ON m.estado_id = e.estado_id ORDER BY e.nombre_estado, m.nombre_municipio";
    $stmt = $pdo->query($sql);
    $municipios = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar los municipios: " . $e->getMessage();
}

// Obtener lista de estados para el select
try {
    $sql = "SELECT estado_id, nombre_estado FROM estado ORDER BY nombre_estado";
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
    <title>Gestionar Municipios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <h1>Gestionar Municipios</h1>

    <?php if ($error_message): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="action" value="add">
        <label for="nombre_municipio">Nuevo Municipio:</label>
        <input type="text" id="nombre_municipio" name="nombre_municipio" required>
        <label for="estado_id">Estado:</label>
        <select id="estado_id" name="estado_id" required>
            <option value="">Seleccione un estado</option>
            <?php foreach ($estados as $estado): ?>
                <option value="<?php echo $estado['estado_id']; ?>"><?php echo htmlspecialchars($estado['nombre_estado']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Agregar Municipio</button>
    </form>

    <h2>Lista de Municipios</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($municipios as $municipio): ?>
                <tr>
                    <td><?php echo htmlspecialchars($municipio['nombre_municipio']); ?></td>
                    <td><?php echo htmlspecialchars($municipio['nombre_estado']); ?></td>
                    <td>
                        <form action="" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="municipio_id" value="<?php echo $municipio['municipio_id']; ?>">
                            <input type="text" name="nombre_municipio" value="<?php echo htmlspecialchars($municipio['nombre_municipio']); ?>" required>
                            <select name="estado_id" required>
                                <option value="">Seleccione un estado</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado['estado_id']; ?>" <?php echo ($estado['estado_id'] == $municipio['estado_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($estado['nombre_estado']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-edit">Editar</button>
                        </form>
                        <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Está seguro de eliminar este municipio?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="municipio_id" value="<?php echo $municipio['municipio_id']; ?>">
                            <button type="submit" class="btn btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>