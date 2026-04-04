<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

// Access Control: Only Management can see reports
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Recepcionista')) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$reporte = [
    'financiero' => [
        'ingreso_bruto' => 0,
        'perdida_cancelacion' => 0
    ],
    'ocupacion' => []
];

// 1. Ingreso Bruto (Basado en Pagos Reales)
$res_ingresos = $conexion->query("SELECT SUM(monto) as total FROM pagos");
if ($row = $res_ingresos->fetch_assoc()) {
    $reporte['financiero']['ingreso_bruto'] = $row['total'] ?? 0;
}

// 2. Pérdida por Cancelación (Basado en Reservas Canceladas)
// Se calcula 1.5 veces el precio horario por cada bloque de 90 min cancelado
$res_perdidas = $conexion->query("SELECT SUM(c.precio_hora * 1.5) as total 
                                  FROM reservas r 
                                  JOIN canchas c ON r.cancha_id = c.id 
                                  WHERE r.status = 'Cancelada'");
if ($row = $res_perdidas->fetch_assoc()) {
    $reporte['financiero']['perdida_cancelacion'] = $row['total'] ?? 0;
}

// 3. Tasa de Ocupación por Cancha
$res_ocupacion = $conexion->query("SELECT c.nombre, 
                                    (COUNT(r.id) * 1.5 / 240) * 100 as tasa 
                                    FROM canchas c 
                                    LEFT JOIN reservas r ON c.id = r.cancha_id AND r.status IN ('Confirmada', 'Finalizada')
                                    GROUP BY c.id");
while ($row = $res_ocupacion->fetch_assoc()) {
    $reporte['ocupacion'][] = [
        'nombre' => $row['nombre'],
        'tasa' => round($row['tasa'], 2)
    ];
}

echo json_encode($reporte);
$conexion->close();
?>
