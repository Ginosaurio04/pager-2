<?php
session_start();
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $player_name = $_POST['player_name'];
    $cancha_id = $_POST['cancha_id'];
    $fecha = date('Y-m-d');
    $hora_inicio = date('H:i:s');
    $hora_fin = date('H:i:s', strtotime('+90 minutes')); // Registro rápido de 90 min

    if (empty($player_name) || empty($cancha_id)) {
        header("Location: citas.html?error=incomplete");
        exit();
    }

    // --- VALIDACIÓN DE CONFLICTO PROFESIONAL (OVERBOOKING) ---
    $sql_conflicto = "SELECT id FROM reservas 
                      WHERE cancha_id = ? 
                      AND fecha = ? 
                      AND status != 'Cancelada'
                      AND (
                          (hora_inicio < ? AND hora_fin > ?)
                          OR (hora_inicio >= ? AND hora_inicio < ?)
                          OR (hora_fin > ? AND hora_fin <= ?)
                      )";
    
    $stmt_check = $conexion->prepare($sql_conflicto);
    $stmt_check->bind_param("isssssss", $cancha_id, $fecha, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        header("Location: citas.html?error=occupied");
        exit();
    }

    // Insertar registro rápido (VINCULADO AL USUARIO ACTUAL SI EXISTE)
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $conexion->prepare("INSERT INTO reservas (player_name, cancha_id, fecha, hora_inicio, hora_fin, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $player_name, $cancha_id, $fecha, $hora_inicio, $hora_fin, $user_id);

    if ($stmt->execute()) {
        header("Location: citas.html?success=1");
    } else {
        echo "Error: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
