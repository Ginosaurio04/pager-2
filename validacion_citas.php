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
        die("Error: Información incompleta. <a href='citas.html'>Volver</a>");
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
        die("ERROR: La cancha está ocupada actualmente para un bloque de 90 min. <a href='citas.html'>Volver</a>");
    }

    // Insertar registro rápido (usuario_id NULL para walk-ins)
    $stmt = $conexion->prepare("INSERT INTO reservas (player_name, cancha_id, fecha, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $player_name, $cancha_id, $fecha, $hora_inicio, $hora_fin);

    if ($stmt->execute()) {
        header("Location: citas.html?success=1");
    } else {
        echo "Error: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
