<?php
session_start(); // Inicia la sesión al principio de la página

// Incluir el archivo de conexión a BD
require_once 'includes/db.php'; // Asume que este archivo existe y conecta a $pdo

// Redirigir si ya está logueado
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php'); // O la página principal del panel
    exit();
}

// Procesar el formulario de login si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validar que no estén vacíos (validación básica)
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=1'); // Redirigir con error
        exit();
    }

    try {
        $sql = "SELECT usuario_id, nombre_usuario, contrasena_hash, rol FROM usuarios WHERE nombre_usuario = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el usuario y la contraseña
        if ($user && password_verify($password, $user['contrasena_hash'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['usuario_id'] = $user['usuario_id'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            $_SESSION['rol'] = $user['rol']; // Guardar el rol si lo usas para permisos

            // Redirigir al dashboard o página principal
            header('Location: dashboard.php');
            exit();
        } else {
            // Usuario o contraseña incorrectos
            header('Location: index.php?error=1'); // Redirigir con error
            exit();
        }

    } catch (PDOException $e) {
        // Manejar error de base de datos (en un entorno real, loggear el error)
        echo "Error de base de datos: " . $e->getMessage();
        // O redirigir a una página de error genérica
        // header('Location: error.php?msg=db_error'); exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Gestión PNF</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'includes/menu-publico.php'; ?> <!-- Incluye el menú de navegación publica -->

    <div class="login-container">
        <h1>Sistema de Información de Aldeas y Registro PNF</h1> <!-- Título principal -->
        <h2>Iniciar Sesión</h2>
        <?php
        // Lógica PHP para mostrar mensaje de error si existe
        if (isset($_GET['error'])) {
            echo '<p class="error-message">Usuario o contraseña incorrectos.</p>';
        }
        ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>
</body>
</html>