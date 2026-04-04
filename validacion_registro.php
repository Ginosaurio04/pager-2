<?php
header('Content-Type: application/json'); // --- CABECERA API ---
require 'conex.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $cedula = trim($_POST['cedula']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar contraseñas
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'ERROR: Las contraseñas no coinciden.']);
        exit();
    }

    $passwordhash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conexion->prepare("INSERT INTO usuarios (username, cedula, email, telefono, password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $username, $cedula, $email, $telefono, $passwordhash);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'REGISTRO EXITOSO. Ya puedes iniciar sesión.']);
        exit();
    }
    else {
        echo json_encode(['success' => false, 'message' => 'ERROR: El usuario o email ya están registrados.']);
        exit();
    }

    $conexion->close();
}
?>