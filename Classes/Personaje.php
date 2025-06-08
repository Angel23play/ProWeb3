<?php
class Personaje
{
    private $id;
    private $nombre;
    private $apellido;
    private $fechaNacimiento;
    private $foto;
    private $profesiones = []; // Array de profesiones
    private $nivelExperiencia;

    private $ruta;
    public function __construct($id, $nombre, $apellido, $fechaNacimiento, $foto, $profesiones, $nivelExperiencia)
{
    $this->id = $id;
    $this->nombre = $nombre;
    $this->apellido = $apellido;
    $this->fechaNacimiento = $fechaNacimiento;
    $this->foto = $foto;
    $this->nivelExperiencia = $nivelExperiencia;

    $this->profesiones = [];

    
   if (is_array($profesiones)) {
    foreach ($profesiones as $p) {
        if ($p instanceof Profesion) {
            
            $this->profesiones[] = $p;
        } elseif (is_array($p)) {
            
            $this->profesiones[] = new Profesion(
                $p['id'] ?? '',
                $p['nombre'] ?? '',
                $p['categoria'] ?? '',
                $p['salario'] ?? 0
            );
        }
        
    }
}

}

    public function agregarProfesion($profesion)
    {
        $this->profesiones[] = $profesion;
    }

    public function actualizarProfesion($profesionId, $nuevaProfesion)
    {
        foreach ($this->profesiones as $key => $profesion) {
            if ($profesion->getId() == $profesionId) {
                $this->profesiones[$key] = $nuevaProfesion;
                return true;
            }
        }
        return false;
    }

    public function eliminarProfesion($profesionId)
    {
        foreach ($this->profesiones as $key => $profesion) {
            if ($profesion->getId() == $profesionId) {
                unset($this->profesiones[$key]);
                return true;
            }
        }
        return false;
    }

    public function obtenerProfesiones()
    {
        return $this->profesiones;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getNivelExperiencia()
    {
        return $this->nivelExperiencia;
    }
    public function Crear(Personaje $persona)
    {


        $json = json_encode($persona);
        file_put_contents($this->id, $json);

    }

    public function Eliminar()
    {
        $ruta = $this->ruta . '/' . $this->getId();

        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }


    public function Editar($id)
    {
        if (is_dir($this->ruta) && file_exists($id)) {
            $json = file_get_contents($this->ruta . '/' . $this->id);
            $json = json_decode($json);

            $personaje = new Personaje();

            foreach ($json as $key => $value) {
                $personaje->$key = $value;
            }
        }
        return $personaje;
    }

}



?>