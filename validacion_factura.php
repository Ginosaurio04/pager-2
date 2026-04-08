<?php
session_start();
header('Content-Type: application/json'); // --- CABECERA API (REQUISITO NEGOCIO) ---
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'ERROR: Debe iniciar sesión para realizar reservas.']);
        exit();
    }

    $usuario_id = $_SESSION['user_id'];
    $cancha_id = $_POST['cancha_id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    
    // Pago
    $metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
    $referencia = trim($_POST['referencia'] ?? '');

    // Validación Referencia
    if (!ctype_digit($referencia) && $referencia !== "") {
        echo json_encode(['success' => false, 'message' => 'ERROR: La referencia de pago debe ser numérica.']);
        exit();
    }

    // Regla de 90 Minutos
    $start = strtotime($hora_inicio);
    $end = strtotime($hora_fin);
    $diff_minutes = round(abs($end - $start) / 60);

    if ($diff_minutes != 90) {
        echo json_encode(['success' => false, 'message' => 'ERROR: El sistema solo permite bloques exactos de 90 minutos.']);
        exit();
    }

    // Validación de Conflicto
    $sql_conflicto = "SELECT id FROM reservas 
                      WHERE cancha_id = ? 
                      AND fecha = ? 
                      AND status NOT IN ('Cancelada')
                      AND (
                          (hora_inicio < ? AND hora_fin > ?)
                          OR (hora_inicio >= ? AND hora_inicio < ?)
                          OR (hora_fin > ? AND hora_fin <= ?)
                      )";
    
    $stmt_check = $conexion->prepare($sql_conflicto);
    $stmt_check->bind_param("isssssss", $cancha_id, $fecha, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'ERROR: Solapamiento detectado. La cancha ya está reservada para este bloque horario.']);
        exit();
    }

    // Transacción
    $conexion->begin_transaction();
    try {
        $stmt_res = $conexion->prepare("INSERT INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, status, payment_status) VALUES (?, ?, ?, ?, ?, 'Confirmada', 'Pagado')");
        $stmt_res->bind_param("iisss", $usuario_id, $cancha_id, $fecha, $hora_inicio, $hora_fin);
        $stmt_res->execute();
        $reserva_id = $stmt_res->insert_id;

        $res_precio = $conexion->query("SELECT precio_hora FROM canchas WHERE id = $cancha_id");
        $precio = $res_precio->fetch_assoc()['precio_hora'] * 1.5;

        $stmt_pago = $conexion->prepare("INSERT INTO pagos (reserva_id, monto, metodo, referencia) VALUES (?, ?, ?, ?)");
        $stmt_pago->bind_param("idss", $reserva_id, $precio, $metodo_pago, $referencia);
        $stmt_pago->execute();

        $conexion->commit();
        echo json_encode(['success' => true, 'message' => 'RESERVA EXITOSA. El bloque horario ha sido asegurado.']);

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['success' => false, 'message' => 'ERROR CRÍTICO: ' . $e->getMessage()]);
    }

    $conexion->close();
}
?>
