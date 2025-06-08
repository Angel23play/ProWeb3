<?php
require_once __DIR__ . '/../Classes/Personaje.php';
require_once __DIR__ . '/../Classes/Profesiones.php';

// Estas son profesiones manuales predefinidas al principio para elegir en el formulario de personajes
function cargarProfesiones()
{
    return [
        ['id' => 1, 'nombre' => 'Guerrera/o', 'categoria' => 'Combate', 'salario' => 1500.00],
        ['id' => 2, 'nombre' => 'Maga/o', 'categoria' => 'Magia', 'salario' => 2000.00],
        ['id' => 3, 'nombre' => 'Arquera/o', 'categoria' => 'Combate a distancia', 'salario' => 1400.00],
         ['id' => 4,  'nombre' => 'Ingeniera/o de Robótica',        'categoria' => 'Tecnología',       'salario' => 3500.00],
        ['id' => 5,  'nombre' => 'Desarrolladora/o Web',           'categoria' => 'Tecnología',       'salario' => 3200.00],
        ['id' => 6,  'nombre' => 'Científica/o de Datos',          'categoria' => 'Tecnología',       'salario' => 3700.00],
        ['id' => 7,  'nombre' => 'Pilota/o Espacial',              'categoria' => 'Aventura',         'salario' => 4000.00],
        ['id' => 8,  'nombre' => 'Veterinaria/o de Mascotas',      'categoria' => 'Salud Animal',     'salario' => 2800.00],
        ['id' => 9,  'nombre' => 'Diseñadora/o de Moda',           'categoria' => 'Creatividad',      'salario' => 2500.00],
        ['id' => 10,  'nombre' => 'Chef Profesional',             'categoria' => 'Gastronomía',      'salario' => 2400.00],
        ['id' => 11,  'nombre' => 'Astrónoma/o',                    'categoria' => 'Ciencia',          'salario' => 3600.00],
        ['id' => 12,  'nombre' => 'Médica/o Cirujana/o',              'categoria' => 'Salud',            'salario' => 4200.00],
        ['id' => 13, 'nombre' => 'Cantante Pop Internacional',   'categoria' => 'Entretenimiento',  'salario' => 3000.00],
    ];
}



$accion = $_GET['accion'] ?? 'listar';
$ruta = "./Data/personajes";
if (!file_exists($ruta)) {
    mkdir($ruta, 0777, true);
}

$personajes = [];
$data = []; // para evitar warnings

// GUARDAR
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? uniqid();

    $profesiones = cargarProfesiones();

    $profesionSeleccionada = null;
    $idProfesionSeleccionada = (int) ($_POST['profesion'] ?? 0);

    foreach ($profesiones as $prof) {
        if ($prof['id'] === $idProfesionSeleccionada) {
            $profesionSeleccionada = $prof;
            break;
        }
    }

    if ($profesionSeleccionada === null) {
        $profesionSeleccionada = ['id' => 0, 'nombre' => 'Desconocida'];
    }

    // Leer profesiones existentes si ya hay un personaje guardado
    $archivoPersonaje = "$ruta/$id.json";
    $profesionesExistentes = [];

    if (file_exists($archivoPersonaje)) {
        $contenido = json_decode(file_get_contents($archivoPersonaje), true);
        if (isset($contenido['profesiones']) && is_array($contenido['profesiones'])) {
            $profesionesExistentes = $contenido['profesiones'];
        }
    }

    // Evitar duplicar la profesión si ya está
    $existe = false;
    foreach ($profesionesExistentes as $prof) {
        if ($prof['id'] == $profesionSeleccionada['id']) {
            $existe = true;
            break;
        }
    }
    if (!$existe) {
        $profesionesExistentes[] = $profesionSeleccionada;
    }

    $data = [
        "id" => $id,
        "nombre" => $_POST['nombre'],
        "apellido" => $_POST['apellido'],
        "fechaNacimiento" => $_POST['fechaNacimiento'],
        "foto" => $_POST['foto'],
        "profesiones" => $profesionesExistentes,
        "nivelExperiencia" => $_POST['nivelExperiencia']
    ];

    $personaje = new Personaje(
        $data['id'],
        $data['nombre'],
        $data['apellido'],
        $data['fechaNacimiento'],
        $data['foto'],
        $data['profesiones'],
        $data['nivelExperiencia']
    );

    file_put_contents("$ruta/$id.json", json_encode($data, JSON_PRETTY_PRINT));
    header("Location: index.php?vista=personajes");
    exit;
}



// ELIMINAR
if ($accion === 'eliminar') {
    $id = $_GET['id'];
    $archivo = "$ruta/$id.json";
    if (file_exists($archivo)) {
        unlink($archivo);
    }
    header("Location: index.php?vista=personajes");
    exit;
}

// EDITAR
if ($accion === 'editar') {
    $id = $_GET['id'];
    $archivo = "$ruta/$id.json";
    if (file_exists($archivo)) {
        $data = json_decode(file_get_contents($archivo), true);
    }
}

// FORMULARIO LIMPIO EN AÑADIR
if ($accion === 'anadir') {
    $data = []; // limpio para evitar valores antiguos
}

// LISTAR TODOS
foreach (glob("$ruta/*.json") as $archivo) {
    $json = file_get_contents($archivo);
    $personajes[] = json_decode($json, true);
}
?>

<!-- FORMULARIO -->
<h2 class="text-danger text-center mb-4">Gestión de Personajes</h2>

<?php if ($accion === 'anadir' || $accion === 'editar'): ?>
    <?php $formClass = $accion === 'anadir' ? 'border border-success p-4' : 'border border-warning p-4'; ?>
    <form class="<?= $formClass ?>" method="post" action="?vista=personajes&accion=guardar">
        <input type="hidden" name="id" value="<?= $data['id'] ?? uniqid() ?>">

        <div class="mb-2">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $data['nombre'] ?? '' ?>" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?= $data['apellido'] ?? '' ?>" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Fecha de Nacimiento:</label>
            <input type="date" name="fechaNacimiento" value="<?= $data['fechaNacimiento'] ?? '' ?>" class="form-control"
                required>
        </div>

        <div class="mb-2">
            <label>Foto (URL):</label>
            <input type="text" name="foto" value="<?= $data['foto'] ?? '' ?>" class="form-control">
        </div>

        <div class="mb-2">
    <label>Nivel de Experiencia:</label>
    <select name="nivelExperiencia" class="form-select" required>
        <?php
        $niveles = ['Principiante', 'Intermedio', 'Avanzado'];
        $nivelSeleccionado = $data['nivelExperiencia'] ?? '';
        foreach ($niveles as $nivel) {
            $selected = ($nivelSeleccionado === $nivel) ? 'selected' : '';
            echo "<option value=\"$nivel\" $selected>$nivel</option>";
        }
        ?>
    </select>
</div>

        <div class="mb-2">
            <label>Profesión:</label>
            <select name="profesion" class="form-select" <?= ($accion === 'editar') ? 'disabled' : 'required' ?>>
                <option value="">Seleccione una</option>
                <?php
                $profesionesDisponibles = cargarProfesiones();
                $profesionSeleccionadaId = '';

                if ($accion === 'editar' && isset($data['profesiones'][0])) {
                    $profesionSeleccionadaId = $data['profesiones'][0]['id'];
                } elseif ($accion !== 'editar') {
                    $profesionSeleccionadaId = $_POST['profesion'] ?? '';
                }

                foreach ($profesionesDisponibles as $profesion) {
                    $selected = ($profesionSeleccionadaId == $profesion['id']) ? 'selected' : '';
                    echo "<option value='{$profesion['id']}' $selected>{$profesion['nombre']}</option>";
                }
                ?>
            </select>

            <?php if ($accion === 'editar' && $profesionSeleccionadaId): ?>
                <input type="hidden" name="profesion" value="<?= $profesionSeleccionadaId ?>">
            <?php endif; ?>
        </div>



        <button class="btn btn-primary"><?= $accion === 'anadir' ? 'Crear' : 'Actualizar' ?></button>
        <a href="?vista=personajes" class="btn btn-secondary">Cancelar</a>
    </form>

<?php else: ?>

    <!-- LISTADO -->
    <!-- LISTADO -->
    <a href="?vista=personajes&accion=anadir" class="btn btn-success mb-3">+ Añadir nuevo personaje</a>

    <table class="table table-bordered table-hover">
        <thead class="table-danger">
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha Nacimiento</th>
                <th>Foto</th>
                <th>Experiencia</th>
                <th>Profesión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php foreach ($personajes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['apellido']) ?></td>
                    <td><?= htmlspecialchars($p['fechaNacimiento']) ?></td>
                    <td><img src="<?= htmlspecialchars($p['foto']) ?>" width="200" height="150" alt=""></td>
                    <td><?= htmlspecialchars($p['nivelExperiencia']) ?></td>
                    <td>
                        <?php
                        if (isset($p['profesiones']) && is_array($p['profesiones']) && count($p['profesiones']) > 0) {
                            echo htmlspecialchars($p['profesiones'][0]['nombre']);
                        } else {
                            echo 'Desconocida';
                        }
                        ?>
                    </td>

                    <td>
                        <a href="?vista=personajes&accion=editar&id=<?= urlencode($p['id']) ?>"
                            class="btn btn-warning btn-sm">Editar</a>
                        <a href="?vista=personajes&accion=eliminar&id=<?= urlencode($p['id']) ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar este personaje?')">Eliminar</a>
                        <a href="?vista=profesiones&id_personaje=<?= urlencode($p['id']) ?>"
                            class="btn btn-info btn-sm ">Profesiones</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>