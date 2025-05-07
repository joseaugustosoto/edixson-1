<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al inicio de sesión si no está logueado
    header('Location: /edixson-1/index.php');
    exit();
}

echo '<nav>';
    echo '<ul>';
        echo '<li><a href="/edixson-1/dashboard.php" class="menu-link">Inicio</a></li>';
        echo '<li><a href="/edixson-1/ubicaciones/estados.php" class="menu-link">Gestionar Estados</a></li>';
        echo '<li><a href="/edixson-1/ubicaciones/municipios.php" class="menu-link">Gestionar Municipios</a></li>';
        echo '<li><a href="/edixson-1/ubicaciones/parroquias.php" class="menu-link">Gestionar Parroquias</a></li>';
        echo '<li><a href="/edixson-1/ubicaciones/aldeas.php" class="menu-link">Gestionar Aldeas</a></li>';
        echo '<li><a href="/edixson-1/pnf/pnf.php" class="menu-link">Gestionar PNF</a></li>';
        echo '<li><a href="/edixson-1/preinscritos.php" class="menu-link">Listado Preinscritos</a></li>';
        echo '<li><a href="/edixson-1/includes/auth.php?logout=true" class="menu-link">Cerrar Sesión</a></li>';
    echo '</ul>';
echo '</nav>';

?>