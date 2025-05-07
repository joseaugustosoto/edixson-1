<?php
require_once 'includes/db.php';

$municipio_id = $_GET['municipio_id'] ?? 0;

$query = "SELECT parroquia_id, nombre_parroquia FROM parroquia WHERE municipio_id = :municipio_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':municipio_id' => $municipio_id]);

header('Content-Type: application/json'); // Especificar el tipo de contenido como JSON
echo json_encode($stmt->fetchAll());
?>