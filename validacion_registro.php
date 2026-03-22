<?php
require 'conex.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $cedula = trim($_POST['cedula']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "<h3>Error: Las contraseñas no coinciden</h3> <a href='registro.html'>Volver al Registro</a>";
        exit();
    }

    $passwordhash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conexion->prepare("INSERT INTO usuarios (username, cedula, email, telefono, password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $username, $cedula, $email, $telefono, $passwordhash);

    if ($stmt->execute()) {
        echo "<h3>Registro Exitoso</h3> <a href='login.html'>Ir al Login</a>";
    }
    else {
        echo "Error: El usuario o email ya están registrados. <a href='registro.html'>Volver</a>";
    }

    $stmt->close();
}
$conexion->close();
?>