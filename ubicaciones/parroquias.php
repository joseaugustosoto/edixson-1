<?php
// filepath: e:\xampp\htdocs\edixson-1\ubicaciones\parroquias.php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$error_message = '';
$success_message = '';

// Procesar acciones (Agregar, Editar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nombre_parroquia = $_POST['nombre_parroquia'] ?? '';
    $municipio_id = $_POST['municipio_id'] ?? null;
    $parroquia_id = $_POST['parroquia_id'] ?? null;

    try {
        if ($action === 'add' && !empty($nombre_parroquia) && !empty($municipio_id)) {
            $sql = "INSERT INTO parroquia (nombre_parroquia, municipio_id) VALUES (:nombre_parroquia, :municipio_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_parroquia', $nombre_parroquia);
            $stmt->bindParam(':municipio_id', $municipio_id);
            $stmt->execute();
            $success_message = 'Parroquia agregada con éxito.';
        } elseif ($action === 'edit' && !empty($nombre_parroquia) && $parroquia_id) {
            $sql = "UPDATE parroquia SET nombre_parroquia = :nombre_parroquia, municipio_id = :municipio_id WHERE parroquia_id = :parroquia_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_parroquia', $nombre_parroquia);
            $stmt->bindParam(':municipio_id', $municipio_id);
            $stmt->bindParam(':parroquia_id', $parroquia_id);
            $stmt->execute();
            $success_message = 'Parroquia actualizada con éxito.';
        } elseif ($action === 'delete' && $parroquia_id) {
            $sql = "DELETE FROM parroquia WHERE parroquia_id = :parroquia_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':parroquia_id', $parroquia_id);
            $stmt->execute();
            $success_message = 'Parroquia eliminada con éxito.';
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Obtener lista de parroquias
try {
    $sql = "SELECT p.*, m.nombre_municipio FROM parroquia p JOIN municipio m ON p.municipio_id = m.municipio_id ORDER BY m.nombre_municipio, p.nombre_parroquia";
    $stmt = $pdo->query($sql);
    $parroquias = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar las parroquias: " . $e->getMessage();
}

// Obtener lista de municipios para el select
try {
    $sql = "SELECT municipio_id, nombre_municipio FROM municipio ORDER BY nombre_municipio";
    $stmt = $pdo->query($sql);
    $municipios = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar los municipios: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Parroquias</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <h1>Gestionar Parroquias</h1>

    <?php if ($error_message): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="action" value="add">
        <label for="nombre_parroquia">Nueva Parroquia:</label>
        <input type="text" id="nombre_parroquia" name="nombre_parroquia" required>
        <label for="municipio_id">Municipio:</label>
        <select id="municipio_id" name="municipio_id" required>
            <option value="">Seleccione un municipio</option>
            <?php foreach ($municipios as $municipio): ?>
                <option value="<?php echo $municipio['municipio_id']; ?>"><?php echo htmlspecialchars($municipio['nombre_municipio']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Agregar Parroquia</button>
    </form>

    <h2>Lista de Parroquias</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Municipio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parroquias as $parroquia): ?>
                <tr>
                    <td><?php echo htmlspecialchars($parroquia['nombre_parroquia']); ?></td>
                    <td><?php echo htmlspecialchars($parroquia['nombre_municipio']); ?></td>
                    <td>
                        <form action="" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="parroquia_id" value="<?php echo $parroquia['parroquia_id']; ?>">
                            <input type="text" name="nombre_parroquia" value="<?php echo htmlspecialchars($parroquia['nombre_parroquia']); ?>" required>
                            <select name="municipio_id" required>
                                <option value="">Seleccione un municipio</option>
                                <?php foreach ($municipios as $municipio): ?>
                                    <option value="<?php echo $municipio['municipio_id']; ?>" <?php echo ($municipio['municipio_id'] == $parroquia['municipio_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($municipio['nombre_municipio']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-edit">Editar</button>
                        </form>
                        <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Está seguro de eliminar esta parroquia?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="parroquia_id" value="<?php echo $parroquia['parroquia_id']; ?>">
                            <button type="submit" class="btn btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>