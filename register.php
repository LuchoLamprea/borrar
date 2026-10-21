<?php
header("Content-Type: application/json; charset=UTF-8");
include 'conexion.php';

// Leer los datos enviados en formato JSON desde Flutter
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['first_name'], $data['last_name'], $data['email'], $data['password'])) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
    exit();
}

$first_name = $data['first_name'];
$last_name = $data['last_name'];
$email = $data['email'];
$phone = $data['phone'] ?? null;
$role = $data['role'] ?? 'RESIDENT'; // Por defecto es residente
$raw_password = $data['password'];

// Encriptar la contraseña de forma segura
$password_hash = password_hash($raw_password, PASSWORD_BCRYPT);

try {
    // Verificar si el correo ya está registrado
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "El correo electrónico ya está registrado"]);
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash, $role]);

    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado exitosamente"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al registrar el usuario: " . $e->getMessage()
    ]);
}
?>