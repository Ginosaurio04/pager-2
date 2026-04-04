<?php
session_start();
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $player_name = $_POST['player_name'];
    $court = $_POST['court'];
    $booking_day = date('Y-m-d'); // For walk-ins, we use today
    $booking_time = date('H:i');

    if (empty($player_name) || empty($court)) {
        die("Error: Nombre del jugador y cancha son obligatorios. <a href='citas.html'>Volver</a>");
    }

    // Insert into database (usuario_id null for walk-ins)
    $stmt = $conexion->prepare("INSERT INTO reservas (player_name, court, booking_day, booking_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $player_name, $court, $booking_day, $booking_time);

    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Walk-in Confirmado</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-[#162210] text-[#f6f8f5] flex items-center justify-center min-h-screen'>
            <div class='bg-[#162210]/50 border border-[#59f20d]/20 p-10 rounded-2xl text-center shadow-xl'>
                <h1 class='text-4xl font-bold mt-4'>¡Walk-in Registrado!</h1>
                <p class='mt-2 opacity-60'>Jugador: $player_name en $court.</p>
                <a href='citas.html' class='inline-block mt-8 bg-[#59f20d] text-[#162210] font-bold px-8 py-3 rounded-lg'>Volver a Reservas</a>
            </div>
        </body>
        </html>";
    } else {
        echo "Error al registrar walk-in: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: citas.html");
    exit();
}
?>
