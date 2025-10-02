<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos del formulario
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

// Evitar inyección SQL
$usuario = $conexion->real_escape_string($usuario);
$contrasena = $conexion->real_escape_string($contrasena);

// Buscar al atleta en la base de datos
$sql = "SELECT * FROM atletas WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
$resultado = $conexion->query($sql);

// Validar existencia
if ($resultado->num_rows == 1) {
    $atleta = $resultado->fetch_assoc();

    // Guardar datos del usuario en sesión
    $_SESSION['usuario'] = $atleta['usuario'];
    $_SESSION['nombre'] = $atleta['nombre'];
    $_SESSION['correo'] = $atleta['correo'];
    $_SESSION['telefono'] = $atleta['telefono'];

    // Redirigir al menú principal
    header("Location: ../Menu.html");
    exit;
} else {
    echo "<script>
        alert('Usuario o contraseña incorrectos');
        window.location.href = '../Login.html';
    </script>";
}

$conexion->close();
?>
