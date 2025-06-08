<?php
class Profesion{
     private $id;
    private $nombre;
    private $categoria;
    private $salario;

    public function __construct($id, $nombre, $categoria, $salario) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->categoria = $categoria;
        $this->salario = $salario;
    }

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getSalario() {
        return $this->salario;
    }
}

?>