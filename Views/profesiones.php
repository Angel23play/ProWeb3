<?php
require_once __DIR__ . '/../Classes/Personaje.php';
require_once __DIR__ . '/../Classes/Profesiones.php';

$personajeId = $_GET['id_personaje'] ?? null;
if (!$personajeId) {
    die("ID del personaje no especificado.");
}

$rutaArchivo = __DIR__ . "/../Data/personajes/{$personajeId}.json";

if (!file_exists($rutaArchivo)) {
    die("Personaje no encontrado.");
}

function cargarPersonaje($ruta)
{
    $json = file_get_contents($ruta);
    $data = json_decode($json, true);

    if (!isset($data['profesiones']) || !is_array($data['profesiones'])) {
        $data['profesiones'] = [];
        file_put_contents($ruta, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Convertimos cada profesión a un objeto Profesion
    $profesiones = [];
    foreach ($data['profesiones'] as $p) {
        $profesiones[] = new Profesion($p['id'], $p['nombre'], $p['categoria'], $p['salario']);
    }

    $personaje = new Personaje(
        $data['id'],
        $data['nombre'],
        $data['apellido'],
        $data['fechaNacimiento'],
        $data['foto'],
        $profesiones, // ahora es un array de objetos Profesion
        $data['nivelExperiencia']
    );

    return $personaje;
}


function guardarPersonaje($personaje, $ruta)
{
    $profesionesArray = [];
    foreach ($personaje->obtenerProfesiones() as $p) {
        $profesionesArray[] = [
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'categoria' => $p->getCategoria(),
            'salario' => $p->getSalario()
        ];
    }

    $json = json_encode([
        'id' => $personaje->getId(),
        'nombre' => $personaje->getNombre(),
        'apellido' => $personaje->getApellido(),
        'fechaNacimiento' => $personaje->getFechaNacimiento(),
        'foto' => $personaje->getFoto(),
        'nivelExperiencia' => $personaje->getNivelExperiencia(),
        'profesiones' => $profesionesArray
    ], JSON_PRETTY_PRINT);

    file_put_contents($ruta, $json);
}

$personaje = cargarPersonaje($rutaArchivo);
$accion = $_GET['accion'] ?? 'listar';

// VARIABLES PARA FORMULARIO (usadas en añadir y editar)
$formId = '';
$formNombre = '';
$formCategoria = '';
$formSalario = '';

if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Podría ser string o int, según tu diseño
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $salario = (float) $_POST['salario'];

    $nuevaProfesion = new Profesion($id, $nombre, $categoria, $salario);

    $profesiones = $personaje->obtenerProfesiones();
    $existe = false;
    foreach ($profesiones as $p) {
        if ($p->getId() == $id) {
            $personaje->actualizarProfesion($id, $nuevaProfesion);
            $existe = true;
            break;
        }
    }

    if (!$existe) {
        $personaje->agregarProfesion($nuevaProfesion);
    }

    guardarPersonaje($personaje, $rutaArchivo);
    header("Location: ?vista=profesiones&id_personaje=$personajeId");
    exit;
}

if ($accion === 'eliminar') {
    $id = $_GET['profesion_id'];
    $personaje->eliminarProfesion($id);
    guardarPersonaje($personaje, $rutaArchivo);
    header("Location: ?vista=profesiones&id_personaje=$personajeId");
    exit;
}

// NUEVO: Acción editar, para mostrar formulario con datos cargados
if ($accion === 'editar') {
    $id = $_GET['profesion_id'] ?? null;
    if ($id === null) {
        die("ID de profesión no especificado para editar.");
    }
    $profesiones = $personaje->obtenerProfesiones();
    foreach ($profesiones as $p) {
        if ($p->getId() == $id) {
            $formId = $p->getId();
            $formNombre = $p->getNombre();
            $formCategoria = $p->getCategoria();
            $formSalario = $p->getSalario();
            break;
        }
    }
}
?>

<h2 class="text-danger text-center mb-4">Profesiones de <?= htmlspecialchars($personaje->getNombre()) ?></h2>

<a href="?vista=profesiones&id_personaje=<?= $personajeId ?>&accion=anadir" class="btn btn-success mb-3">+ Añadir nueva profesión</a>

<?php if ($accion === 'anadir' || $accion === 'editar'): ?>
    <form method="post" action="?vista=profesiones&id_personaje=<?= $personajeId ?>&accion=guardar"
        class="border p-4 mb-4 <?= $accion === 'anadir' ? 'border-success' : 'border-warning' ?>">
        <h4><?= $accion === 'anadir' ? 'Añadir Profesión' : 'Editar Profesión' ?></h4>
        <div class="mb-3">
            <label>ID:</label>
            <!-- En editar, ID no debería ser editable para evitar inconsistencias -->
            <input type="text" name="id" class="form-control" required
                value="<?= htmlspecialchars($formId) ?>" <?= $accion === 'editar' ? 'readonly' : '' ?>>
        </div>
        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($formNombre) ?>">
        </div>
        <div class="mb-3">
            <label>Categoría:</label>
            <input type="text" name="categoria" class="form-control" required value="<?= htmlspecialchars($formCategoria) ?>">
        </div>
        <div class="mb-3">
            <label>Salario:</label>
            <input type="number" step="0.01" name="salario" class="form-control" required value="<?= htmlspecialchars($formSalario) ?>">
        </div>
        <button class="btn btn-primary">Guardar</button>
        <a href="?vista=profesiones&id_personaje=<?= $personajeId ?>" class="btn btn-secondary">Cancelar</a>
    </form>
<?php endif; ?>

<?php if (count($personaje->obtenerProfesiones()) === 0): ?>
    <p>No hay profesiones registradas para este personaje.</p>
<?php else: ?>
    <table class="table table-bordered table-hover">
        <thead class="table-danger">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Salario</th>
                <th>Acciones</th>   
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personaje->obtenerProfesiones() as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p->getId()) ?></td>
                    <td><?= htmlspecialchars($p->getNombre()) ?></td>
                    <td><?= htmlspecialchars($p->getCategoria()) ?></td>
                    <td><?= number_format($p->getSalario(), 2) ?></td>
                    <td>
                        <a href="?vista=profesiones&id_personaje=<?= $personajeId ?>&accion=editar&profesion_id=<?= urlencode($p->getId()) ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="?vista=profesiones&id_personaje=<?= $personajeId ?>&accion=eliminar&profesion_id=<?= urlencode($p->getId()) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta profesión?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
