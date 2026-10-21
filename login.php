<?php
header("Content-Type: application/json; charset=UTF-8");
include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    echo json_encode(["success" => false, "message" => "Correo y contraseña requeridos"]);
    exit();
}

$email = $data['email'];
$password = $data['password'];

try {
    // Buscar al usuario por su correo
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password_hash, role, is_active FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Credenciales inválidas"]);
        exit();
    }

    if (!$user['is_active']) {
        echo json_encode(["success" => false, "message" => "La cuenta se encuentra inactiva"]);
        exit();
    }

    // Verificar la contraseña contra el hash de la base de datos
    if (password_verify($password, $user['password_hash'])) {
        // No enviamos el hash de regreso por seguridad
        unset($user['password_hash']);

        echo json_encode([
            "success" => true,
            "message" => "Inicio de sesión exitoso",
            "data" => $user
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Credenciales inválidas"]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error en el servidor: " . $e->getMessage()
    ]);
}
?>