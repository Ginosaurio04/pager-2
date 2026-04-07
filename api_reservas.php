<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

$current_user_id = $_SESSION['user_id'] ?? 0;
$user_rol = $_SESSION['rol'] ?? 'Invitado';

// --- TAREA AUTOMÁTICA DE ESTADO (NEGOCIO) ---
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
$data_para_frontend = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        /**
         * [POO: CLASES Y OBJETOS]
         * Instanciamos un objeto de la clase Reserva para manejar la lógica de cada fila.
         */
        $reservaObj = new Reserva(
            $row['id'], 
            $row['usuario_id'], 
            $row['jugador'], 
            $row['cancha'], 
            $row['fecha'], 
            $row['hora'], 
            $row['hora_fin'], 
            $row['estado'], 
            $row['pago']
        );

        /**
         * [POO: ENCAPSULAMIENTO]
         * La lógica de privacidad y permisos de cancelación está encapsulada dentro de toArray().
         */
        $data_para_frontend[] = $reservaObj->toArray($current_user_id, $user_rol);
    }
}

echo json_encode($data_para_frontend);
$conexion->close();
?>
