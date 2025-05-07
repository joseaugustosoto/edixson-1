<?php
require_once '../includes/auth.php';
require_login(); // Protege esta página
require_once '../includes/db.php'; // Conexión PDO disponible como $pdo

// Lógica para obtener la aldea a editar
$aldea_id = $_GET['id'] ?? null;
$aldea = null;

if ($aldea_id) {
    try {
        $sql = "SELECT * FROM aldea WHERE aldea_id = :aldea_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':aldea_id', $aldea_id);
        $stmt->execute();
        $aldea = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error al cargar la aldea: " . $e->getMessage();
    }
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_aldea = $_POST['nombre_aldea'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $parroquia_id = $_POST['parroquia_id'] ?? null;
    $estado = $_POST['estado'] ?? 'Activa';

    if ($aldea) {
        try {
            $sql_update = "UPDATE aldea SET nombre_aldea = :nombre_aldea, direccion = :direccion, parroquia_id = :parroquia_id, estado = :estado WHERE aldea_id = :aldea_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':nombre_aldea', $nombre_aldea);
            $stmt_update->bindParam(':direccion', $direccion);
            $stmt_update->bindParam(':parroquia_id', $parroquia_id);
            $stmt_update->bindParam(':estado', $estado);
            $stmt_update->bindParam(':aldea_id', $aldea_id);
            $stmt_update->execute();

            header('Location: aldeas.php?success=aldea_updated');
            exit();
        } catch (PDOException $e) {
            $error_message = "Error al actualizar la aldea: " . $e->getMessage();
        }
    }
}

// Obtener las parroquias para el selector
$parroquias = [];
try {
    $sql_parroquias = "SELECT parroquia_id, nombre_parroquia FROM parroquia ORDER BY nombre_parroquia";
    $stmt_parroquias = $pdo->query($sql_parroquias);
    $parroquias = $stmt_parroquias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error al cargar las parroquias: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aldea - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <h1>Editar Aldea</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php if ($aldea): ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre_aldea">Nombre de la Aldea:</label>
                <input type="text" id="nombre_aldea" name="nombre_aldea" value="<?php echo htmlspecialchars($aldea['nombre_aldea']); ?>" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($aldea['direccion']); ?>">
            </div>
            <div class="form-group">
                <label for="parroquia_id">Parroquia:</label>
                <select id="parroquia_id" name="parroquia_id" required>
                    <option value="">Seleccione una parroquia</option>
                    <?php foreach ($parroquias as $parroquia): ?>
                        <option value="<?php echo $parroquia['parroquia_id']; ?>" <?php echo ($parroquia['parroquia_id'] == $aldea['parroquia_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parroquia['nombre_parroquia']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="Activa" <?php echo ($aldea['estado'] == 'Activa') ? 'selected' : ''; ?>>Activa</option>
                    <option value="Inactiva" <?php echo ($aldea['estado'] == 'Inactiva') ? 'selected' : ''; ?>>Inactiva</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Aldea</button>
        </form>
    <?php else: ?>
        <p>No se encontró la aldea.</p>
    <?php endif; ?>
</body>
</html>