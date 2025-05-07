<?php
// Datos de conexión a la base de datos
$db_host = 'localhost'; // O la IP/hostname de tu servidor de BD
$db_name = 'gestion_pnf';
$db_user = 'root'; // Tu usuario de base de datos
$db_pass = ''; // Tu contraseña de base de datos
$db_charset = 'utf8mb4'; // O 'utf8'

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Traer resultados como array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas nativas (recomendado)
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un error y detiene el script
    // En producción, deberías loggear esto y mostrar un mensaje amigable
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
    // Para desarrollo puedes usar: die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>