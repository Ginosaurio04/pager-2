<?php
session_start();
header('Content-Type: application/json'); // --- CABECERA API (ESTÁNDAR) ---
include 'conex.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, rol FROM usuarios WHERE username = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // --- VERIFICACIÓN SEGURA ---
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['rol'] = $user['rol'];

            $redirect = ($user['rol'] === 'Administrador' || $user['rol'] === 'Recepcionista') ? "panel de control.html" : "citas.html";
            
            echo json_encode(['success' => true, 'redirect' => $redirect]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'El usuario no existe.']);
        exit();
    }
}
$conexion->close();
?>