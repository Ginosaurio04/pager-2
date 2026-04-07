<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "pager";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

/**
 * --- CARGADOR DE CLASES (POO) ---
 * [POO]: Facilita el uso de objetos sin necesidad de requiere manuales extensos.
 */
spl_autoload_register(function ($nombre_clase) {
    // Intentamos cargar la clase desde el directorio /classes/
    $archivo = __DIR__ . "/classes/" . $nombre_clase . ".php";
    if (file_exists($archivo)) {
        require_once $archivo;
    }
});
?>