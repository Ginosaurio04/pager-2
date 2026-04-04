-- PadelPro Manager - Professional Business Engine
-- All-in-One Database Schema

CREATE DATABASE IF NOT EXISTS pager;
USE pager;

-- Tabla de Usuarios (POO: Herencia ficticia mediante Roles)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('Jugador', 'Recepcionista', 'Administrador') DEFAULT 'Jugador',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Canchas (Atributos Físicos)
CREATE TABLE IF NOT EXISTS canchas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    superficie VARCHAR(50) DEFAULT 'Grama Sintética',
    techada BOOLEAN DEFAULT TRUE,
    estado ENUM('Disponible', 'Mantenimiento', 'Limpieza') DEFAULT 'Disponible',
    precio_hora DECIMAL(10, 2) NOT NULL
);

-- Tabla de Reservas (Entidad Transaccional Central)
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    player_name VARCHAR(100) NULL,
    cancha_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    status ENUM('Pendiente', 'Confirmada', 'Cancelada', 'Finalizada') DEFAULT 'Pendiente',
    payment_status ENUM('Pagado', 'Por Pagar') DEFAULT 'Por Pagar',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (cancha_id) REFERENCES canchas(id)
);

-- Tabla de Pagos (Auditoría Financiera)
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo ENUM('Pago Móvil', 'Efectivo', 'Transferencia', 'Tarjeta') NOT NULL,
    referencia VARCHAR(100) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
);

-- Datos Iniciales de Prueba (Alineación de Negocio)
INSERT IGNORE INTO usuarios (username, cedula, email, telefono, password, rol) VALUES 
('admin', '12345678', 'admin@padelpro.com', '123456', '123456', 'Administrador'),
('recepcion', '55555555', 'staff@padelpro.com', '112233', '123456', 'Recepcionista'),
('juanperez', '87654321', 'juan@gmail.com', '654321', '123456', 'Jugador');

INSERT IGNORE INTO canchas (nombre, superficie, techada, precio_hora) VALUES 
('Cancha #1 (Principal)', 'Grama Pro', TRUE, 25.00),
('Cancha #2', 'Grama Estándar', FALSE, 20.00),
('Cancha #3 (Panorámica)', 'Grama Pro', TRUE, 30.00);

-- Ejemplo de Reservas para hoy
INSERT IGNORE INTO reservas (usuario_id, cancha_id, fecha, hora_inicio, hora_fin, status, payment_status) VALUES 
(3, 1, CURDATE(), '16:00:00', '17:30:00', 'Confirmada', 'Pagado'),
(3, 2, CURDATE(), '18:00:00', '19:30:00', 'Pendiente', 'Por Pagar');
