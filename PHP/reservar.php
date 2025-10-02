<?php
session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario'])) {
    echo "<script>
        alert('Debes iniciar sesión primero.');
        window.location.href = '../Login.html';
    </script>";
    exit;
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$usuario = $_SESSION['usuario'];
$id_atleta = null;

$stmt = $conexion->prepare("SELECT id_atleta FROM atletas WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($id_atleta);
$stmt->fetch();
$stmt->close();

if (!$id_atleta) {
    echo "<script>
        alert('No se encontró el atleta asociado al usuario.');
        window.history.back();
    </script>";
    $conexion->close();
    exit;
}

$servicios     = $_POST['servicios']     ?? $_POST['servicio'] ?? null;
$fecha_reserva = $_POST['fecha_reserva'] ?? $_POST['fecha']    ?? null;

if (!$servicios || !$fecha_reserva) {
    echo "<script>
        alert('Faltan datos: servicio o fecha.');
        window.history.back();
    </script>";
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare("
    INSERT INTO reservaciones (id_atleta, servicios, fecha_reserva, estado_reserva)
    VALUES (?, ?, ?, 'Pendiente')
");
if (!$stmt) {
    echo "<script>
        alert('Error preparando la reservación.');
        window.history.back();
    </script>";
    $conexion->close();
    exit;
}
$stmt->bind_param("iss", $id_atleta, $servicios, $fecha_reserva);

if ($stmt->execute()) {
    echo "<script>
        alert('Reservación realizada con éxito.');
        window.location.href = '../Menu.html';
    </script>";
} else {
    echo "<script>
        alert('Error al realizar la reservación.');
        window.history.back();
    </script>";
}

$stmt->close();
$conexion->close();
?>