<?php
require_once('../conexion.php'); 

// Lógica para Crear/Actualizar un Atleta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se está editando
    if (isset($_POST['id_atleta']) && !empty($_POST['id_atleta'])) {
        $id_atleta = $_POST['id_atleta'];
        $identificacion = $_POST['identificacion'];
        $usuario = $_POST['usuario'];
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];

        $stmt = $conexion->prepare("UPDATE atletas SET identificacion = ?, usuario = ?, nombre = ?, apellido1 = ?, apellido2 = ?, correo = ?, telefono = ? WHERE id_atleta = ?");
        $stmt->bind_param("sssssssi", $identificacion, $usuario, $nombre, $apellido1, $apellido2, $correo, $telefono, $id_atleta);

        if ($stmt->execute()) {
            header('Location: atletas.php?mensaje=Atleta actualizado con éxito.');
        } else {
            header('Location: atletas.php?error=Error al actualizar el atleta: ' . $stmt->error);
        }
        $stmt->close();
        exit;
    } else { // Si se está creando
        $identificacion = $_POST['identificacion'];
        $usuario = $_POST['usuario'];
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $rol = 'atleta';

        // **Validación para evitar duplicados de identificación**
        $stmt_check = $conexion->prepare("SELECT identificacion FROM atletas WHERE identificacion = ?");
        $stmt_check->bind_param("s", $identificacion);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            header('Location: atletas.php?error=La identificación ya está registrada.');
            $stmt_check->close();
            exit;
        }
        $stmt_check->close();

        $stmt = $conexion->prepare("INSERT INTO atletas (identificacion, usuario, contrasena, nombre, apellido1, apellido2, correo, telefono, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $identificacion, $usuario, $contrasena, $nombre, $apellido1, $apellido2, $correo, $telefono, $rol);

        if ($stmt->execute()) {
            header('Location: atletas.php?mensaje=Atleta agregado con éxito.');
        } else {
            header('Location: atletas.php?error=Error al agregar el atleta: ' . $stmt->error);
        }
        $stmt->close();
        exit;
    }
}

// Lógica para Eliminar un Atleta
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {
    $id_atleta = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM atletas WHERE id_atleta = ?");
    $stmt->bind_param("i", $id_atleta);

    if ($stmt->execute()) {
        header('Location: atletas.php?mensaje=Atleta eliminado con éxito.');
    } else {
        header('Location: atletas.php?error=Error al eliminar el atleta: ' . $stmt->error);
    }
    $stmt->close();
    exit;
}

// Lógica para obtener el atleta a editar
$atleta_a_editar = null;
if (isset($_GET['accion']) && $_GET['accion'] === 'editar' && isset($_GET['id'])) {
    $id_atleta = $_GET['id'];
    $stmt = $conexion->prepare("SELECT * FROM atletas WHERE id_atleta = ?");
    $stmt->bind_param("i", $id_atleta);
    $stmt->execute();
    $result = $stmt->get_result();
    $atleta_a_editar = $result->fetch_assoc();
    $stmt->close();
}

// Incluye el header después de toda la lógica PHP
require_once('includes/header.php');

$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';

?>

<div class="main-content">
    
    <h2>Gestión de Atletas</h2>
    
    <?php if (!empty($mensaje)): ?>
        <p class="mensaje-exito"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="form-container">
        <h2><?= ($atleta_a_editar) ? 'Editar Atleta' : 'Agregar Nuevo Atleta' ?></h2>
        <form action="atletas.php" method="POST">
            <?php if ($atleta_a_editar): ?>
                <input type="hidden" name="id_atleta" value="<?= htmlspecialchars($atleta_a_editar['id_atleta']) ?>">
            <?php endif; ?>
            
            <label>Identificación:</label><input type="text" name="identificacion" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['identificacion']) : '' ?>" required><br>
            <label>Usuario:</label><input type="text" name="usuario" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['usuario']) : '' ?>" required><br>
            <?php if (!$atleta_a_editar): ?>
                <label>Contraseña:</label><input type="password" name="contrasena" required><br>
            <?php endif; ?>
            <label>Nombre:</label><input type="text" name="nombre" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['nombre']) : '' ?>" required><br>
            <label>Apellido 1:</label><input type="text" name="apellido1" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['apellido1']) : '' ?>" required><br>
            <label>Apellido 2:</label><input type="text" name="apellido2" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['apellido2']) : '' ?>" required><br>
            <label>Correo:</label><input type="email" name="correo" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['correo']) : '' ?>" required><br>
            <label>Teléfono:</label><input type="tel" name="telefono" value="<?= ($atleta_a_editar) ? htmlspecialchars($atleta_a_editar['telefono']) : '' ?>" required><br>
            <button type="submit"><?= ($atleta_a_editar) ? 'Actualizar Atleta' : 'Agregar Atleta' ?></button>
            <?php if ($atleta_a_editar): ?>
                <a href="atletas.php">Cancelar</a>
            <?php endif; ?>
        </form>
    </div>

    <hr>

    <h2>Atletas Registrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Identificación</th>
                <th>Usuario</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM atletas";
            $result = $conexion->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_atleta'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['identificacion']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre']) . " " . htmlspecialchars($row['apellido1']) . " " . htmlspecialchars($row['apellido2']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                    echo "<td><a href='atletas.php?accion=editar&id=" . $row['id_atleta'] . "'>Editar</a> | <a href='atletas.php?accion=eliminar&id=" . $row['id_atleta'] . "' onclick='return confirm(\"¿Estás seguro de eliminar a este atleta?\")'>Eliminar</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No hay atletas registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
require_once('includes/footer.php');
?>