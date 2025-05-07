<?php
// Este archivo contiene funciones útiles para autenticación

function require_login() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Asegura que la sesión esté iniciada
    }
    if (!isset($_SESSION['usuario_id'])) {
        // No hay sesión activa, redirigir al login
        header('Location: ../index.php'); // Asegúrate que la ruta sea correcta
        exit();
    }
    // Opcional: podrías agregar lógica para verificar permisos basados en $_SESSION['rol']
}

// Función para cerrar sesión
function logout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Asegura que la sesión esté iniciada
    }
    session_unset();   // Elimina todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header('Location: ../index.php'); // Redirigir al login
    exit();
}

// Verifica si se solicita cerrar sesión
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    logout();
}
?>