<?php
require_once 'EntidadBase.php';

/**
 * Clase Usuario: Representa a un usuario del sistema.
 * [CLASES Y OBJETOS, ENCAPSULAMIENTO]
 */
class Usuario extends EntidadBase {
    // Propiedades privadas para ENCAPSULAMIENTO
    private $username;
    private $email;
    private $rol;
    private $cedula;

    public function __construct($id, $username, $email, $rol, $cedula = '') {
        parent::__construct($id);
        $this->username = $username;
        $this->email = $email;
        $this->rol = $rol;
        $this->cedula = $cedula;
    }

    // Getters públicos para acceder a propiedades privadas de forma segura
    public function getUsername() {
        return $this->username;
    }

    public function getRol() {
        return $this->rol;
    }

    public function getEmail() {
        return $this->email;
    }

    // [POLIMORFISMO POTENCIAL]: Método que será sobrescrito en Administrador
    public function obtenerFirma() {
        return "Atentamente, " . $this->username . " (Usuario Estándar)";
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'rol' => $this->rol,
            'cedula' => $this->cedula
        ];
    }
}
?>
