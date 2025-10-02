<?php
require_once('../conexion.php'); 

$mensaje = '';
$error = '';

// Lógica para Crear o Actualizar una Zona Deportiva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se está actualizando (hay un id_zona)
    if (isset($_POST['id_zona']) && !empty($_POST['id_zona'])) {
        $id_zona = $_POST['id_zona'];
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $tipo_zona = $_POST['tipo_zona'];
        $instructor_a_cargo = $_POST['instructor_a_cargo'];
        $costo = $_POST['costo'];
        
        $stmt = $conexion->prepare("UPDATE zonas_deportivas SET codigo = ?, nombre = ?, tipo_zona = ?, instructor_a_cargo = ?, costo = ? WHERE id_zona = ?");
        $stmt->bind_param("ssssdi", $codigo, $nombre, $tipo_zona, $instructor_a_cargo, $costo, $id_zona);

        if ($stmt->execute()) {
            header('Location: menu.php?mensaje=Zona deportiva actualizada con éxito.');
        } else {
            header('Location: menu.php?error=Error al actualizar la zona: ' . $stmt->error);
        }
        $stmt->close();
        exit;
    } else { // Si se está creando
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $tipo_zona = $_POST['tipo_zona'];
        $instructor_a_cargo = $_POST['instructor_a_cargo'];
        $costo = $_POST['costo'];

        $stmt = $conexion->prepare("INSERT INTO zonas_deportivas (codigo, nombre, tipo_zona, instructor_a_cargo, costo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $codigo, $nombre, $tipo_zona, $instructor_a_cargo, $costo);

        if ($stmt->execute()) {
            header('Location: menu.php?mensaje=Zona deportiva agregada con éxito.');
        } else {
            header('Location: menu.php?error=Error al agregar la zona: ' . $stmt->error);
        }
        $stmt->close();
        exit;
    }
}

// Lógica para Eliminar una Zona Deportiva
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {
    $id_zona = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM zonas_deportivas WHERE id_zona = ?");
    $stmt->bind_param("i", $id_zona);

    if ($stmt->execute()) {
        header('Location: menu.php?mensaje=Zona deportiva eliminada con éxito.');
    } else {
        header('Location: menu.php?error=Error al eliminar la zona: ' . $stmt->error);
    }
    $stmt->close();
    exit;
}

// Lógica para obtener la zona a editar
$zona_a_editar = null;
if (isset($_GET['accion']) && $_GET['accion'] === 'editar' && isset($_GET['id'])) {
    $id_zona = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM zonas_deportivas WHERE id_zona = ?");
    $stmt->bind_param("i", $id_zona);
    $stmt->execute();
    $result = $stmt->get_result();
    $zona_a_editar = $result->fetch_assoc();
    $stmt->close();
}
require_once('includes/header.php');
$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';

?>

<h2>Gestión de Zonas Deportivas</h2>

<?php if (!empty($mensaje)): ?>
    <p class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="form-container">
    <h2><?= ($zona_a_editar) ? 'Editar Zona' : 'Agregar Nueva Zona' ?></h2>
    <form action="menu.php" method="POST">
        <?php if ($zona_a_editar): ?>
            <input type="hidden" name="id_zona" value="<?= htmlspecialchars($zona_a_editar['id_zona']) ?>">
        <?php endif; ?>
        <label>Zona:</label><input type="text" name="codigo" value="<?= ($zona_a_editar) ? htmlspecialchars($zona_a_editar['codigo']) : '' ?>" required><br>
        <label>Área:</label><input type="text" name="nombre" value="<?= ($zona_a_editar) ? htmlspecialchars($zona_a_editar['nombre']) : '' ?>" required><br>
        <label>Tipo de Zona:</label><input type="text" name="tipo_zona" value="<?= ($zona_a_editar) ? htmlspecialchars($zona_a_editar['tipo_zona']) : '' ?>" required><br>
        <label>Instructor a Cargo:</label><input type="text" name="instructor_a_cargo" value="<?= ($zona_a_editar) ? htmlspecialchars($zona_a_editar['instructor_a_cargo']) : '' ?>" required><br>
        <label>Costo de la Clase:</label><input type="number" name="costo" step="0.01" value="<?= ($zona_a_editar) ? htmlspecialchars($zona_a_editar['costo']) : '' ?>" required><br>
        
        <button type="submit"><?= ($zona_a_editar) ? 'Actualizar Zona' : 'Agregar Zona' ?></button>
        <?php if ($zona_a_editar): ?>
            <a href="menu.php">Cancelar</a>
        <?php endif; ?>
    </form>

</div>

<hr>

<h2>Zonas Existentes</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Zona</th>
            <th>Área</th>
            <th>Tipo de Zona</th>
            <th>Instructor</th>
            <th>Costo de la Clase</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM zonas_deportivas";
        $result = $conexion->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_zona'] . "</td>";
                echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tipo_zona']) . "</td>";
                echo "<td>" . htmlspecialchars($row['instructor_a_cargo']) . "</td>";
                echo "<td>₡ " . number_format($row['costo'], 2, '.', ',') . "</td>";
                echo "<td><a href='menu.php?accion=editar&id=" . $row['id_zona'] . "'>Editar</a> | <a href='menu.php?accion=eliminar&id=" . $row['id_zona'] . "' onclick='return confirm(\"¿Estás seguro?\")'>Eliminar</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay zonas deportivas registradas.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
require_once('includes/footer.php');
?>