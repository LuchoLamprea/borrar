<?php
// Datos de conexión
$host = 'localhost';
$port = '5432';
$dbname = 'prueba';
$user = 'postgres';
$password = 'prueba123';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla, devolvemos un JSON con el error y detenemos la ejecución
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos: " . $e->getMessage()
    ]);
    exit();
}
?>