<?php
require_once 'includes/db.php';

if (isset($_GET['pnf_id'])) {
    $pnfId = intval($_GET['pnf_id']);

    $query = "SELECT aldea.aldea_id, aldea.nombre_aldea 
              FROM aldea_pnf 
              JOIN aldea ON aldea_pnf.aldea_id = aldea.aldea_id 
              WHERE aldea_pnf.pnf_id = :pnf_id AND aldea.estado = 'Activa'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pnf_id', $pnfId, PDO::PARAM_INT);
    $stmt->execute();

    $aldeas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($aldeas);
}