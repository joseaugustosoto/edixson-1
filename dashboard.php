<?php
session_start();
require_once 'includes/auth.php';
require_login();
require_once 'includes/db.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión PNF</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/menu.php'; ?>

    <main>
        <h2>Opciones Disponibles</h2>
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <a href="/edixson-1/ubicaciones/estados.php" class="">Gestionar Estados</a> | 
            <a href="/edixson-1/ubicaciones/municipios.php" class="">Gestionar Municipios</a> | 
            <a href="/edixson-1/ubicaciones/parroquias.php" class="">Gestionar Parroquias </a> | 
            <a href="/edixson-1/ubicaciones/aldeas.php" class="">Gestionar Aldeas</a> | 
            <a href="/edixson-1/pnf/pnf.php" class="">Gestionar PNF</a>
        </div>
        <p>Desde aquí puedes gestionar las Aldeas Universitarias y los Programas Nacionales de Formación (PNF).</p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gestión PNF. Todos los derechos reservados.</p>
    </footer>
</body>
</html>