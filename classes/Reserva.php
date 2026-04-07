<?php
require_once 'EntidadBase.php';

/**
 * Clase Reserva: Representa una reserva en el sistema.
 * [CLASES Y OBJETOS]
 */
class Reserva extends EntidadBase {
    private $usuario_id;
    private $nombre;
    private $cancha;
    private $fecha;
    private $hora;
    private $hora_fin;
    private $estado;
    private $pago;

    public function __construct(
        $id, $usuario_id, $nombre, $cancha, $fecha, $hora, $hora_fin, $estado, $pago
    ) {
        parent::__construct($id);
        $this->usuario_id = $usuario_id;
        $this->nombre = $nombre;
        $this->cancha = $cancha;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->hora_fin = $hora_fin;
        $this->estado = $estado;
        $this->pago = $pago;
    }

    public function toArray($usuarioActualId = 0, $rolActual = 'Invitado') {
        // [ENCAPSULAMIENTO DE LÓGICA DE NEGOCIO]: No cualquiera puede ver quién reservó
        $nombreMostrado = $this->nombre;
        if ($rolActual === 'Jugador' && $this->usuario_id != $usuarioActualId) {
            $nombreMostrado = "Cupo Reservado";
        }

        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'jugador' => $nombreMostrado,
            'cancha' => $this->cancha,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'hora_fin' => $this->hora_fin,
            'estado' => $this->estado,
            'pago' => $this->pago,
            'start_iso' => $this->fecha . 'T' . $this->hora,
            'end_iso' => $this->fecha . 'T' . $this->hora_fin,
            'can_cancel' => $this->puedeCancelar($usuarioActualId, $rolActual)
        ];
    }

    private function puedeCancelar($usuarioActualId, $rolActual) {
        return ($rolActual === 'Administrador' || $rolActual === 'Recepcionista' || ($rolActual === 'Jugador' && $this->usuario_id == $usuarioActualId));
    }
}
?>
