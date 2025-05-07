<?php
require_once 'includes/db.php';
session_start();
require_once 'includes/auth.php';
require_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = $_GET['id'];

// Obtener los datos del registro
$stmt = $pdo->prepare("SELECT * FROM datos_personales dp
    JOIN direccion_habitacion dh ON dp.datos_personales_id = dh.datos_personales_id
    JOIN preinscripcion pr ON dp.datos_personales_id = pr.datos_personales_id
    WHERE dp.cedula = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_STR);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Registro no encontrado.");
}

// Actualizar los datos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primer_nombre = $_POST['primer_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $telefono_celular = $_POST['telefono_celular'];
    $correo = $_POST['correo'];

    $updateStmt = $pdo->prepare("UPDATE datos_personales dp
        JOIN direccion_habitacion dh ON dp.datos_personales_id = dh.datos_personales_id
        SET dp.primer_nombre = :primer_nombre,
            dp.primer_apellido = :primer_apellido,
            dh.telefono_celular = :telefono_celular,
            dh.correo = :correo
        WHERE dp.cedula = :id");
    $updateStmt->bindParam(':primer_nombre', $primer_nombre);
    $updateStmt->bindParam(':primer_apellido', $primer_apellido);
    $updateStmt->bindParam(':telefono_celular', $telefono_celular);
    $updateStmt->bindParam(':correo', $correo);
    $updateStmt->bindParam(':id', $id, PDO::PARAM_STR);

    if ($updateStmt->execute()) {
        $_SESSION['success_message'] = "Registro actualizado correctamente.";
        header("Location: preinscritos.php");
        exit;
    } else {
        $error_message = "Error al actualizar el registro.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/menu.php'; ?>
    
    <h1>Editar Registro</h1>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?= $error_message ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="primer_nombre">Primer Nombre:</label>
        <input type="text" name="primer_nombre" id="primer_nombre" value="<?= htmlspecialchars($record['primer_nombre']) ?>" required>

        <label for="primer_apellido">Primer Apellido:</label>
        <input type="text" name="primer_apellido" id="primer_apellido" value="<?= htmlspecialchars($record['primer_apellido']) ?>" required>

        <label for="telefono_celular">Teléfono Celular:</label>
        <input type="text" name="telefono_celular" id="telefono_celular" value="<?= htmlspecialchars($record['telefono_celular']) ?>" required>

        <label for="correo">Correo:</label>
        <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($record['correo']) ?>" required>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</body>
</html>