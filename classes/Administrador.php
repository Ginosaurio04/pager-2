<?php
require_once 'Usuario.php';

/**
 * Clase Administrador: Representa a un usuario con privilegios elevados.
 * [HERENCIA]: Hereda de Usuario para reutilizar su código básico.
 */
class Administrador extends Usuario {
    private $privilegiosExtra = true;

    // [POLIMORFISMO EN ACCIÓN]: Sobrescribimos el comportamiento de la clase padre (obtenerFirma)
    public function obtenerFirma() {
        return "Gerencia: " . $this->getUsername() . " (Administrador Senior)";
    }

    public function ejecutarAccionAdministrativa($accion) {
        // Lógica específica de administrador
        return "Acción de administrador ejecutada: " . $accion;
    }

    public function toArray() {
        $baseData = parent::toArray();
        $baseData['extra'] = $this->privilegiosExtra;
        return $baseData;
    }
}
?>
