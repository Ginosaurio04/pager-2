<?php
session_start();
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("Debe iniciar sesión para realizar una reserva. <a href='login.html'>Volver al Login</a>");
    }

    $usuario_id = $_SESSION['user_id'];
    $court = $_POST['court'];
    $booking_day = $_POST['booking_day'];
    $booking_time = $_POST['booking_time'];

    // Check if fields are not empty
    if (empty($court) || empty($booking_day) || empty($booking_time)) {
        die("Error: Todos los campos son obligatorios. <a href='factura.html'>Volver</a>");
    }

    // Insert into database
    $stmt = $conexion->prepare("INSERT INTO reservas (usuario_id, court, booking_day, booking_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $court, $booking_day, $booking_time);

    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Reserva Confirmada</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-[#162210] text-[#f6f8f5] flex items-center justify-center min-h-screen'>
            <div class='bg-[#162210]/50 border border-[#59f20d]/20 p-10 rounded-2xl text-center shadow-xl'>
                <span class='text-6xl text-[#59f20d]'>check_circle</span>
                <h1 class='text-4xl font-bold mt-4'>¡Reserva Exitosa!</h1>
                <p class='mt-2 opacity-60'>Tu cancha ha sido reservada para el $booking_day a las $booking_time.</p>
                <a href='index.html' class='inline-block mt-8 bg-[#59f20d] text-[#162210] font-bold px-8 py-3 rounded-lg'>Volver al Inicio</a>
            </div>
        </body>
        </html>";
    } else {
        echo "Error al procesar la reserva: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: factura.html");
    exit();
}
?>
