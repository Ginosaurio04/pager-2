<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$rol = $_SESSION['rol'] ?? 'Invitado';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $reserva_id = (int)$_POST['id'];
    $action = $_POST['action'];

    $is_admin = ($rol === 'Administrador' || $rol === 'Recepcionista');

    if (!$is_admin) {
        if ($rol !== 'Jugador' || $action !== 'cancel') {
            echo json_encode(['error' => 'Permisos insuficientes']);
            exit();
        }

        // Verify that the reservation belongs to the Jugador
        $stmt_check = $conexion->prepare("SELECT usuario_id FROM reservas WHERE id = ?");
        $stmt_check->bind_param("i", $reserva_id);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        $reserva_db = $res->fetch_assoc();
        $stmt_check->close();

        if (!$reserva_db || $reserva_db['usuario_id'] != $user_id) {
            echo json_encode(['error' => 'No puedes cancelar la reserva de otro usuario']);
            exit();
        }
    }

    $nuevo_estado = '';
    if ($action === 'cancel') {
        $nuevo_estado = 'Cancelada';
    } elseif ($action === 'confirm') {
        $nuevo_estado = 'Confirmada';
    }

    if ($nuevo_estado !== '') {
        $stmt = $conexion->prepare("UPDATE reservas SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $reserva_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'status' => $nuevo_estado]);
        } else {
            echo json_encode(['error' => $conexion->error]);
        }
        $stmt->close();
    }
}

$conexion->close();
?>
