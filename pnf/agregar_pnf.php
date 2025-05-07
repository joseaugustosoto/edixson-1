<?php
session_start(); // Inicia la sesión al principio de la página

// Incluir el archivo de conexión a BD
require_once '../includes/db.php'; // Asume que este archivo existe y conecta a $pdo
require_once '../includes/auth.php'; // Asegura que el usuario esté autenticado
require_login(); // Protege esta página

// Inicializar variables
$codigo_pnf = '';
$nombre_pnf = '';
$descripcion = '';
$error_message = '';

// Obtener las Aldeas activas para mostrarlas en el formulario
try {
    $sql_aldeas = "SELECT aldea_id, nombre_aldea FROM aldea WHERE estado = 'Activa' ORDER BY nombre_aldea";
    $stmt_aldeas = $pdo->query($sql_aldeas);
    $aldeas = $stmt_aldeas->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al cargar las Aldeas: " . $e->getMessage();
}

// Procesar el formulario de agregar PNF si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_pnf = $_POST['codigo_pnf'] ?? '';
    $nombre_pnf = $_POST['nombre_pnf'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $aldeas_seleccionadas = $_POST['aldeas'] ?? []; // Aldeas seleccionadas

    if (empty($nombre_pnf)) {
        $error_message = 'El nombre del PNF es obligatorio.';
    } else {
        try {
            // Insertar el PNF
            $pdo->beginTransaction();
            $sql = "INSERT INTO pnf (codigo_pnf, nombre_pnf, descripcion) VALUES (:codigo_pnf, :nombre_pnf, :descripcion)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':codigo_pnf', $codigo_pnf);
            $stmt->bindParam(':nombre_pnf', $nombre_pnf);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->execute();
            $pnf_id = $pdo->lastInsertId();

            // Insertar las relaciones en aldea_pnf
            $sql_aldea_pnf = "INSERT INTO aldea_pnf (aldea_id, pnf_id) VALUES (:aldea_id, :pnf_id)";
            $stmt_aldea_pnf = $pdo->prepare($sql_aldea_pnf);
            foreach ($aldeas_seleccionadas as $aldea_id) {
                $stmt_aldea_pnf->bindParam(':aldea_id', $aldea_id);
                $stmt_aldea_pnf->bindParam(':pnf_id', $pnf_id);
                $stmt_aldea_pnf->execute();
            }

            $pdo->commit();
            header('Location: pnf.php?success=pnf_added');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Error al agregar PNF: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar PNF - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>
    <h1>Agregar Nuevo PNF</h1>

    <?php if ($error_message): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <!-- Campos existentes -->
        <div class="form-group">
            <label for="codigo_pnf">Código PNF:</label>
            <input type="text" id="codigo_pnf" name="codigo_pnf" value="<?php echo htmlspecialchars($codigo_pnf); ?>">
        </div>
        <div class="form-group">
            <label for="nombre_pnf">Nombre PNF:</label>
            <input type="text" id="nombre_pnf" name="nombre_pnf" required value="<?php echo htmlspecialchars($nombre_pnf); ?>">
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>
        </div>

        <!-- Nuevo campo para seleccionar Aldeas -->
        <div class="form-group">
            <label for="aldeas">Aldeas donde se imparte:</label>
            <select id="aldeas" name="aldeas[]" multiple>
                <?php foreach ($aldeas as $aldea): ?>
                    <option value="<?php echo $aldea['aldea_id']; ?>">
                        <?php echo htmlspecialchars($aldea['nombre_aldea']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Agregar PNF</button>
    </form>

    <p><a href="pnf.php" class="btn btn-info">Volver a la lista de PNF</a></p>
</body>
</html>