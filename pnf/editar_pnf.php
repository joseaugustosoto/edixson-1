<?php
require_once '../includes/auth.php';
require_login(); // Protege esta página
require_once '../includes/db.php'; // Conexión PDO disponible como $pdo

// Lógica para obtener el PNF a editar
$pnf_id = $_GET['id'] ?? null;
$pnf = null;
$aldeas = [];
$aldeas_seleccionadas = [];

if ($pnf_id) {
    try {
        $sql = "SELECT pnf_id, codigo_pnf, nombre_pnf, descripcion, estado FROM pnf WHERE pnf_id = :pnf_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pnf_id', $pnf_id);
        $stmt->execute();
        $pnf = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener todas las Aldeas activas
        $sql_aldeas = "SELECT aldea_id, nombre_aldea FROM aldea WHERE estado = 'Activa' ORDER BY nombre_aldea";
        $stmt_aldeas = $pdo->query($sql_aldeas);
        $aldeas = $stmt_aldeas->fetchAll();

        // Obtener las Aldeas relacionadas con el PNF
        $sql_aldeas_pnf = "SELECT aldea_id FROM aldea_pnf WHERE pnf_id = :pnf_id";
        $stmt_aldeas_pnf = $pdo->prepare($sql_aldeas_pnf);
        $stmt_aldeas_pnf->bindParam(':pnf_id', $pnf_id);
        $stmt_aldeas_pnf->execute();
        $aldeas_seleccionadas = $stmt_aldeas_pnf->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $error_message = "Error al cargar el PNF: " . $e->getMessage();
    }
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_pnf = $_POST['codigo_pnf'] ?? '';
    $nombre_pnf = $_POST['nombre_pnf'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estado = $_POST['estado'] ?? 'Activo';
    $aldeas_seleccionadas = $_POST['aldeas'] ?? [];

    if (empty($nombre_pnf)) {
        $error_message = "El nombre del PNF es obligatorio.";
    } else {
        try {
            $pdo->beginTransaction();

            // Actualizar el PNF
            $sql_update = "UPDATE pnf SET codigo_pnf = :codigo_pnf, nombre_pnf = :nombre_pnf, descripcion = :descripcion, estado = :estado WHERE pnf_id = :pnf_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':codigo_pnf', $codigo_pnf);
            $stmt_update->bindParam(':nombre_pnf', $nombre_pnf);
            $stmt_update->bindParam(':descripcion', $descripcion);
            $stmt_update->bindParam(':estado', $estado);
            $stmt_update->bindParam(':pnf_id', $pnf_id);
            $stmt_update->execute();

            // Eliminar relaciones existentes en aldea_pnf
            $sql_delete_aldeas = "DELETE FROM aldea_pnf WHERE pnf_id = :pnf_id";
            $stmt_delete_aldeas = $pdo->prepare($sql_delete_aldeas);
            $stmt_delete_aldeas->bindParam(':pnf_id', $pnf_id);
            $stmt_delete_aldeas->execute();

            // Insertar nuevas relaciones en aldea_pnf
            $sql_insert_aldeas = "INSERT INTO aldea_pnf (aldea_id, pnf_id) VALUES (:aldea_id, :pnf_id)";
            $stmt_insert_aldeas = $pdo->prepare($sql_insert_aldeas);
            foreach ($aldeas_seleccionadas as $aldea_id) {
                $stmt_insert_aldeas->bindParam(':aldea_id', $aldea_id);
                $stmt_insert_aldeas->bindParam(':pnf_id', $pnf_id);
                $stmt_insert_aldeas->execute();
            }

            $pdo->commit();
            header('Location: pnf.php?success=pnf_updated');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Error al actualizar el PNF: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar PNF - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../includes/menu.php'; ?>

    <h1>Editar PNF</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php if ($pnf): ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="codigo_pnf">Código PNF:</label>
                <input type="text" id="codigo_pnf" name="codigo_pnf" value="<?php echo htmlspecialchars($pnf['codigo_pnf']); ?>">
            </div>
            <div class="form-group">
                <label for="nombre_pnf">Nombre PNF:</label>
                <input type="text" id="nombre_pnf" name="nombre_pnf" required value="<?php echo htmlspecialchars($pnf['nombre_pnf']); ?>">
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($pnf['descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="Activo" <?php echo ($pnf['estado'] === 'Activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="Inactivo" <?php echo ($pnf['estado'] === 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="aldeas">Aldeas donde se imparte:</label>
                <select id="aldeas" name="aldeas[]" multiple>
                    <?php foreach ($aldeas as $aldea): ?>
                        <option value="<?php echo $aldea['aldea_id']; ?>" 
                            <?php echo in_array($aldea['aldea_id'], $aldeas_seleccionadas) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($aldea['nombre_aldea']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar PNF</button>
        </form>
    <?php else: ?>
        <p>No se encontró el PNF especificado.</p>
    <?php endif; ?>
</body>
</html>