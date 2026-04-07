<?php
/**
 * Clase Abstracta: EntidadBase
 * Representa la base para cualquier objeto persistente en la base de datos.
 * [ABSTRACCIÓN]: No se puede instanciar directamente, define la estructura común.
 */
abstract class EntidadBase {
    protected $id;
    protected $fecha_creacion;

    public function __construct($id = null) {
        $this->id = $id;
    }

    // Método abstracto que obliga a las clases hijas a implementar su lógica de conversión a JSON/Array
    abstract public function toArray();

    public function getId() {
        return $this->id;
    }
}
?>
