<?php
session_start();
header('Content-Type: application/json');
include 'conex.php';

// Access Control: Only Admins can perform lifecycle operations
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Recepcionista')) {
    echo json_encode(['error' => 'Permisos insuficientes']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $reserva_id = (int)$_POST['id'];
    $action = $_POST['action'];

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
