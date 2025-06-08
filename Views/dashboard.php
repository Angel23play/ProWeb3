<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php

require("./Classes/Profesiones.php");
require("./Classes/Personaje.php");
require("./Classes/Dashboard.php");

$ruta = "./Data/personajes";
$archivos = scandir($ruta);

$profesiones = [];

$ruta = "./Data/personajes";
$archivos = scandir($ruta);
$dashboard = new Dashboard();

$profesiones = [];

foreach ($archivos as $archivo) {
    if ($archivo !== '.' && $archivo !== '..' && pathinfo($archivo, PATHINFO_EXTENSION) === 'json') {
        $contenido = file_get_contents($ruta . '/' . $archivo);
        $datos = json_decode($contenido, true);

        $personaje = new Personaje(
            $datos['id'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fechaNacimiento'],
            $datos['foto'],
            $profesiones,
            $datos['nivelExperiencia']
        );

        if (isset($datos['profesiones'])) {
            foreach ($datos['profesiones'] as $p) {
                $profesion = new Profesion($p['id'], $p['nombre'], $p['categoria'], $p['salario']);
                $personaje->agregarProfesion($profesion);
            }
        }

        $dashboard->agregarPersonaje($personaje);
    }
}

// Mostrar profesiones sin errores




$cantidadPer = $dashboard->cantidadPersonajes();
$cantidadPro = $dashboard->cantidadProfesiones();
$edadPromedio = $dashboard->edadPromedio();
$salarioPromedio = $dashboard->salarioPromedio();
$profesionAlta = $dashboard->profesionMasAlta()->GetNombre();
$profesionBaja = $dashboard->profesionMasBaja()->GetNombre();
$ExperienciaPromedio = $dashboard->nivelExperienciaMasComun();
$personajeMasPagado = $dashboard->PersonajeMasPagado();
$categoriaMasPopular =$dashboard->categoriaMasPopular();

$profesionesAgrupadas = [];

foreach ($dashboard->personajes as $personaje) {
    foreach ($personaje->obtenerProfesiones() as $profesion) {
        $nombre = $profesion->getNombre();
        $salario = $profesion->getSalario();

        if (!isset($profesionesAgrupadas[$nombre])) {
            $profesionesAgrupadas[$nombre] = [
                'total' => 0,
                'count' => 0
            ];
        }

        $profesionesAgrupadas[$nombre]['total'] += $salario;
        $profesionesAgrupadas[$nombre]['count']++;
    }
}

$labels = [];
$salarios = [];

foreach ($profesionesAgrupadas as $nombre => $data) {
    $labels[] = $nombre;
    $salarios[] = round($data['total'] / $data['count'], 2);
}



?>
<!-- DASHBOARD -->
<div class="container my-5">
    <h2 class="text-danger text-center  mb-4" ">📊 Panel de Estadísticas - Mundo Barbie</h2>

    <!-- Estadísticas principales -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card text-white bg-pink shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Personajes</h5>
                    <p class="display-6 fw-bold"> <?php echo $cantidadPer ?> </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Profesiones</h5>
                    <p class="display-6 fw-bold"><?php echo $cantidadPro ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Edad Promedio</h5>
                    <p class="display-6 fw-bold"><?php echo $edadPromedio ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas adicionales -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Categoría más popular</h6>
                    <p class="fw-bold text-pink"><?php echo $categoriaMasPopular ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Nivel de experiencia común</h6>
                    <p class="fw-bold text-pink"><?php echo $ExperienciaPromedio ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Profesión mejor pagada</h6>
                    <p class="fw-bold text-pink"><?php echo $profesionAlta ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Salarios -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Salario promedio</h6>
                    <p class="fw-bold text-pink"><?php echo $salarioPromedio ?>$USD</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Personaje mejor pagado</h6>
                    <p class="fw-bold text-pink"><?php echo $personajeMasPagado['nombre'] ?></p>
                     <p class="fw-bold text-pink"><?php echo $personajeMasPagado['salario']  ?>$USD</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Distribucion de personajes por categoria</h6>
                    <ul class="list-unstyled">
                        <?php
                        $categorias = $dashboard->distribucionPorCategoria();
                        foreach ($categorias as $nombre => $cantidad) {
                            echo "<li><strong>$nombre</strong>: $cantidad personaje(s)</li>";
                        }
                        ?>
                    </ul>

                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-pink text-white text-center">
            <h4 class="mb-0">Distribución de Salarios por Profesión</h4>
        </div>
        <div class="card-body bg-light">
            <canvas id="salaryChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    const ctx = document.getElementById("salaryChart").getContext("2d");
    const colores = [
        'rgba(255, 99, 132, 0.7)',   // rojo
        'rgba(54, 162, 235, 0.7)',   // azul
        'rgba(255, 206, 86, 0.7)',   // amarillo
        'rgba(75, 192, 192, 0.7)',   // verde agua
        'rgba(153, 102, 255, 0.7)',  // morado
        'rgba(255, 159, 64, 0.7)',   // naranja
        'rgba(199, 199, 199, 0.7)',  // gris claro
        'rgba(83, 102, 255, 0.7)',   // azul oscuro
        'rgba(255, 99, 255, 0.7)',   // rosa
        'rgba(99, 255, 132, 0.7)',   // verde claro
    ];
    const labels = <?php echo json_encode($labels); ?>;
    const data = <?php echo json_encode($salarios); ?>;
    
    const backgroundColors = labels.map((_, i) => colores[i % colores.length]);
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Salario Promedio ($USD)",
                data: data,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
            borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: "#333",
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        color: "#333"
                    },
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        color: "#333"
                    }
                }
            }
        }
    });
</script>

<!-- Estilo personalizado opcional -->
<style>
    .bg-pink {
        background-color: #ff69b4 !important;
    }

    .text-pink {
        color: #ff69b4 !important;
    }
</style>