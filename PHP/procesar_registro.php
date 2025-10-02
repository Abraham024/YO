<?php
$conexion = new mysqli("localhost", "root", "", "dashboard_db");

if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

$identificacion = $_POST['identificacion'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];

$sql = "INSERT INTO atletas (identificacion, usuario, contrasena, nombre, apellido1, apellido2, correo, telefono) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssss", $identificacion, $usuario, $contrasena, $nombre, $apellido1, $apellido2, $correo, $telefono);

if ($stmt->execute()) {
    header("Location: ../login.html?registro=exitoso");
    exit();
} else {
    echo "Error al registrar: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
