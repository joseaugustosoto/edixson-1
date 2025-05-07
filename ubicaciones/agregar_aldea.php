<?php
session_start();
require_once '../includes/auth.php';
require_login(); // Protege esta página
require_once '../includes/db.php'; // Conexión PDO disponible como $pdo

// Lógica para manejar el formulario de agregar aldea
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_aldea = $_POST['nombre_aldea'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $parroquia_id = $_POST['parroquia_id'] ?? null;

    // Validar que los campos no estén vacíos
    if (empty($nombre_aldea) || empty($parroquia_id)) {
        $error_message = "Por favor, complete todos los campos requeridos.";
    } else {
        try {
            $sql = "INSERT INTO aldea (nombre_aldea, direccion, parroquia_id) VALUES (:nombre_aldea, :direccion, :parroquia_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_aldea', $nombre_aldea);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':parroquia_id', $parroquia_id);
            $stmt->execute();

            header('Location: aldeas.php?success=aldea_added');
            exit();
        } catch (PDOException $e) {
            $error_message = "Error al agregar la aldea: " . $e->getMessage();
        }
    }
}

// Obtener las parroquias para el selector
try {
    $sql = "SELECT parroquia_id, nombre_parroquia FROM parroquia ORDER BY nombre_parroquia";
    $stmt = $pdo->query($sql);
    $parroquias = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar las parroquias: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Aldea - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Agregar Nueva Aldea</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="nombre_aldea">Nombre de la Aldea:</label>
            <input type="text" id="nombre_aldea" name="nombre_aldea" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion">
        </div>
        <div class="form-group">
            <label for="parroquia_id">Parroquia:</label>
            <select id="parroquia_id" name="parroquia_id" required>
                <option value="">Seleccione una parroquia</option>
                <?php foreach ($parroquias as $parroquia): ?>
                    <option value="<?php echo $parroquia['parroquia_id']; ?>">
                        <?php echo htmlspecialchars($parroquia['nombre_parroquia']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Aldea</button>
    </form>

    <p><a href="aldeas.php" class="btn btn-info">Volver a la lista de Aldeas</a></p>
</body>
</html>