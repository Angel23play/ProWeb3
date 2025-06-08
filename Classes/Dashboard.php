<?php

class Dashboard {
    //Esta es la data del dashboard 
    public $personajes = [];

    public function agregarPersonaje($personaje) {
        $this->personajes[] = $personaje;
    }

    public function cantidadPersonajes() {
        return count($this->personajes);
    }

    public function cantidadProfesiones() {
        $profesiones = [];
        foreach ($this->personajes as $personaje) {
            foreach ($personaje->obtenerProfesiones() as $profesion) {
                $profesiones[] = $profesion;
            }
        }
        return count($profesiones);
    }

    public function edadPromedio() {
        $totalEdad = 0;
        $totalPersonas = count($this->personajes);
        foreach ($this->personajes as $personaje) {
            $fechaNacimiento = new DateTime($personaje->getFechaNacimiento());
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
            $totalEdad += $edad;
        }
        return $totalPersonas > 0 ? $totalEdad / $totalPersonas : 0;
    }

    public function distribucionPorCategoria() {
        $distribucion = [];
        foreach ($this->personajes as $personaje) {
            foreach ($personaje->obtenerProfesiones() as $profesion) {
                $categoria = $profesion->getCategoria();
                if (!isset($distribucion[$categoria])) {
                    $distribucion[$categoria] = 0;
                }
                $distribucion[$categoria]++;
            }
        }
        return $distribucion;
    }

  public function nivelExperienciaMasComun() {
    $niveles = [];
    foreach ($this->personajes as $personaje) {
        $nivel = $personaje->getNivelExperiencia();
        if (!isset($niveles[$nivel])) {
            $niveles[$nivel] = 0;
        }
        $niveles[$nivel]++;
    }
    arsort($niveles);
    return key($niveles); // Retorna el nivel con más personajes
}


    public function salarioPromedio() {
        $totalSalario = 0;
        $totalProfesiones = 0;
        foreach ($this->personajes as $personaje) {
            foreach ($personaje->obtenerProfesiones() as $profesion) {
                $totalSalario += $profesion->getSalario();
                $totalProfesiones++;
            }
        }
        return $totalProfesiones > 0 ? $totalSalario / $totalProfesiones : 0;
    }

    public function profesionMasAlta() {
        $maxSalario = 0;
        $profesionMax = null;
        foreach ($this->personajes as $personaje) {
            foreach ($personaje->obtenerProfesiones() as $profesion) {
                if ($profesion->getSalario() > $maxSalario) {
                    $maxSalario = $profesion->getSalario();
                    $profesionMax = $profesion;
                }
            }
        }
        return $profesionMax;
    }

    public function profesionMasBaja() {
        $minSalario = PHP_INT_MAX;
        $profesionMin = null;
        foreach ($this->personajes as $personaje) {
            foreach ($personaje->obtenerProfesiones() as $profesion) {
                if ($profesion->getSalario() < $minSalario) {
                    $minSalario = $profesion->getSalario();
                    $profesionMin = $profesion;
                }
            }
        }
        return $profesionMin;
    }

public function PersonajeMasPagado() {
    $nombre = "";
    $topSalario = 0;

    foreach ($this->personajes as $personaje) {
        foreach ($personaje->obtenerProfesiones() as $profesion) {
            $salario = $profesion->getSalario();

            if ($salario > $topSalario) {
                $topSalario = $salario;
                $nombre = $personaje->getNombre(); // Asegúrate que tu clase Personaje tiene getNombre()
            }
        }
    }

    $data = [
        'nombre' => $nombre,
        'salario' => $topSalario
    ];

    return $data;
}

public function categoriaMasPopular() {
    $distribucion = $this->distribucionPorCategoria();
    if (empty($distribucion)) {
        return null; // o "" si prefieres
    }
    arsort($distribucion); // ordena de mayor a menor
    return key($distribucion); // devuelve la categoría con más personajes
}

}
