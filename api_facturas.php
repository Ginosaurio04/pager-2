<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$rol = $_SESSION['rol'] ?? 'Jugador';

$sql = "SELECT p.id as pago_id, p.monto, p.metodo, p.referencia, p.fecha_pago,
               r.fecha, r.hora_inicio, r.hora_fin,
               c.nombre as cancha_nombre,
               COALESCE(u.username, r.player_name) as jugador_nombre,
               u.cedula as jugador_cedula
        FROM pagos p
        JOIN reservas r ON p.reserva_id = r.id
        JOIN canchas c ON r.cancha_id = c.id
        LEFT JOIN usuarios u ON r.usuario_id = u.id";

// Si no es admin o recepción, filtrar solo por su usuario_id
if ($rol !== 'Administrador' && $rol !== 'Recepcionista') {
    $sql .= " WHERE r.usuario_id = $user_id";
}

$sql .= " ORDER BY p.fecha_pago DESC";

$result = $conexion->query($sql);
$facturas = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $facturas[] = $row;
    }
}

echo json_encode($facturas);
$conexion->close();
?>
