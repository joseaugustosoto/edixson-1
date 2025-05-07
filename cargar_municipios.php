<?php
require_once 'includes/db.php';

$estado_id = $_GET['estado_id'] ?? 0;

$query = "SELECT municipio_id, nombre_municipio FROM municipio WHERE estado_id = :estado_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':estado_id' => $estado_id]);

header('Content-Type: application/json'); // Especificar el tipo de contenido como JSON
echo json_encode($stmt->fetchAll());
?>