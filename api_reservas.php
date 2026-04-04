<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

$current_user_id = $_SESSION['user_id'] ?? 0;
$user_rol = $_SESSION['rol'] ?? 'Invitado';

// --- TAREA AUTOMÁTICA DE ESTADO (NEGOCIO) ---
// Actualizamos a 'Finalizada' si el tiempo ya pasó (Respetando el bloque de 90 min)
$sql_auto_finalize = "UPDATE reservas SET status = 'Finalizada' 
                      WHERE status = 'Confirmada' 
                      AND (fecha < CURDATE() OR (fecha = CURDATE() AND hora_fin < TIME(NOW())))";
$conexion->query($sql_auto_finalize);

$sql = "SELECT r.id, r.usuario_id, COALESCE(r.player_name, u.username) AS jugador, c.nombre AS cancha, r.fecha, r.hora_inicio as hora, r.hora_fin, r.status AS estado, r.payment_status AS pago 
        FROM reservas r
        LEFT JOIN usuarios u ON r.usuario_id = u.id
        LEFT JOIN canchas c ON r.cancha_id = c.id
        ORDER BY r.fecha DESC, r.hora_inicio DESC LIMIT 100";

$result = $conexion->query($sql);
$reservas = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // --- POLÍTICA DE PRIVACIDAD ---
        if ($user_rol === 'Jugador' && $row['usuario_id'] != $current_user_id) {
            $row['jugador'] = "Cupo Reservado";
        }

        $row['can_cancel'] = ($user_rol === 'Administrador' || $user_rol === 'Recepcionista' || ($user_rol === 'Jugador' && $row['usuario_id'] == $current_user_id));

        $row['start_iso'] = $row['fecha'] . 'T' . $row['hora'];
        $row['end_iso'] = $row['fecha'] . 'T' . $row['hora_fin'];
        
        $reservas[] = $row;
    }
}

echo json_encode($reservas);
$conexion->close();
?>
