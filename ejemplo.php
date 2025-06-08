<?php

require("./Classes/Profesiones.php");
require("./Classes/Personaje.php");
require("./Classes/Dashboard.php");
// Crear profesiones
$profesion1 = new Profesion(1, "Veterinaria", "Ciencia", 5000);
$profesion2 = new Profesion(2, "Piloto", "Deporte", 8000);
$profesion3 = new Profesion(3, "Diseñadora de Moda", "Arte", 6000);

// Crear personajes
$personaje1 = new Personaje(1, "Barbie", "Roberts", "1980-03-09", "foto1.jpg", "Avanzado");
$personaje1->agregarProfesion($profesion1);
$personaje1->agregarProfesion($profesion2);

$personaje2 = new Personaje(2, "Ken", "Roberts", "1982-04-15", "foto2.jpg", "Intermedio");
$personaje2->agregarProfesion($profesion3);

// Crear Dashboard
$dashboard = new Dashboard();
$dashboard->agregarPersonaje($personaje1);
$dashboard->agregarPersonaje($personaje2);

// Obtener estadísticas
echo "Cantidad de personajes: " . $dashboard->cantidadPersonajes() . "\n";
echo "Cantidad de profesiones: " . $dashboard->cantidadProfesiones() . "\n";
echo "Edad promedio: " . $dashboard->edadPromedio() . "\n";
echo "Nivel de experiencia más común: " . $dashboard->nivelExperienciaMasComun() . "\n";
echo "Salario promedio: $" . $dashboard->salarioPromedio() . "\n";
echo "Profesión más alta: " . $dashboard->profesionMasAlta()->getNombre() . "\n";
echo "Profesión más baja: " . $dashboard->profesionMasBaja()->getNombre() . "\n";
?>