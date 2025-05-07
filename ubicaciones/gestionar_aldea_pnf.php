<?php
require_once '../includes/auth.php';
require_login(); // Protege esta página
require_once '../includes/db.php'; // Conexión PDO disponible como $pdo

// Lógica PHP para obtener las aldeas y PNF
try {
    // Obtener todas las aldeas
    $sql_aldeas = "SELECT aldea_id, nombre_aldea FROM aldea WHERE estado = 'Activa'";
    $stmt_aldeas = $pdo->query($sql_aldeas);
    $aldeas = $stmt_aldeas->fetchAll();

    // Obtener todos los PNF
    $sql_pnf = "SELECT pnf_id, nombre_pnf FROM pnf WHERE estado = 'Activo'";
    $stmt_pnf = $pdo->query($sql_pnf);
    $pnf_list = $stmt_pnf->fetchAll();

    // Manejar la asignación de PNF a aldeas
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $aldea_id = $_POST['aldea_id'] ?? null;
        $pnf_id = $_POST['pnf_id'] ?? null;

        if ($aldea_id && $pnf_id) {
            try {
                // Verificar si ya existe la relación
                $sql_check = "SELECT * FROM aldea_pnf WHERE aldea_id = :aldea_id AND pnf_id = :pnf_id";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->bindParam(':aldea_id', $aldea_id);
                $stmt_check->bindParam(':pnf_id', $pnf_id);
                $stmt_check->execute();

                if ($stmt_check->rowCount() === 0) {
                    // Insertar nueva relación
                    $sql_insert = "INSERT INTO aldea_pnf (aldea_id, pnf_id) VALUES (:aldea_id, :pnf_id)";
                    $stmt_insert = $pdo->prepare($sql_insert);
                    $stmt_insert->bindParam(':aldea_id', $aldea_id);
                    $stmt_insert->bindParam(':pnf_id', $pnf_id);
                    $stmt_insert->execute();
                    header('Location: gestionar_aldea_pnf.php?success=assigned');
                    exit();
                } else {
                    header('Location: gestionar_aldea_pnf.php?error=already_assigned');
                    exit();
                }
            } catch (PDOException $e) {
                $error_message = "Error al asignar PNF a la aldea: " . $e->getMessage();
            }
        }
    }

} catch (PDOException $e) {
    $error_message = "Error al cargar datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Aldea-PNF - Gestión PNF</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/menu.php'; ?>

    <h1>Gestionar Aldea-PNF</h1>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">PNF asignado a la aldea con éxito.</p>
    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'already_assigned'): ?>
        <p class="error-message">Este PNF ya está asignado a la aldea seleccionada.</p>
    <?php endif; ?>

    <form action="" method="POST">
        <div>
            <label for="aldea_id">Seleccionar Aldea:</label>
            <select name="aldea_id" id="aldea_id" required>
                <option value="">Seleccione una aldea</option>
                <?php foreach ($aldeas as $aldea): ?>
                    <option value="<?php echo $aldea['aldea_id']; ?>"><?php echo htmlspecialchars($aldea['nombre_aldea']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="pnf_id">Seleccionar PNF:</label>
            <select name="pnf_id" id="pnf_id" required>
                <option value="">Seleccione un PNF</option>
                <?php foreach ($pnf_list as $pnf): ?>
                    <option value="<?php echo $pnf['pnf_id']; ?>"><?php echo htmlspecialchars($pnf['nombre_pnf']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="action" value="assign">Asignar PNF a Aldea</button>
    </form>

    <h2>Relaciones Existentes</h2>
    <table>
        <thead>
            <tr>
                <th>Aldea</th>
                <th>PNF</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Obtener relaciones existentes
            $sql_relaciones = "SELECT a.nombre_aldea, p.nombre_pnf 
                               FROM aldea_pnf ap 
                               JOIN aldea a ON ap.aldea_id = a.aldea_id 
                               JOIN pnf p ON ap.pnf_id = p.pnf_id";
            $stmt_relaciones = $pdo->query($sql_relaciones);
            $relaciones = $stmt_relaciones->fetchAll();

            if (empty($relaciones)): ?>
                <tr>
                    <td colspan="2">No hay relaciones asignadas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($relaciones as $relacion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($relacion['nombre_aldea']); ?></td>
                        <td><?php echo htmlspecialchars($relacion['nombre_pnf']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>