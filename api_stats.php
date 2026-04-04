<?php
header('Content-Type: application/json');
include 'conex.php';

$response = [
    'total_usuarios' => 0,
    'total_reservas' => 0,
    'ingresos' => 0
];

// Total usuarios
$res = $conexion->query("SELECT COUNT(*) as count FROM usuarios");
if($res) $response['total_usuarios'] = $res->fetch_assoc()['count'];

// Total reservas
$res = $conexion->query("SELECT COUNT(*) as count FROM reservas");
if($res) $response['total_reservas'] = $res->fetch_assoc()['count'];

// Ingresos reales basados en canchas.precio_hora
$sql_ingresos = "SELECT SUM(c.precio_hora * 1.5) as total 
                 FROM reservas r 
                 JOIN canchas c ON r.cancha_id = c.id 
                 WHERE r.payment_status = 'Pagado'";
$res_ingresos = $conexion->query($sql_ingresos);
if($res_ingresos) $response['ingresos'] = (float)$res_ingresos->fetch_assoc()['total'];

echo json_encode($response);
$conexion->close();
?>
