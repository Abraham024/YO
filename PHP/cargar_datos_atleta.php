<?php
session_start();

// Verificar si el atleta ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No se ha iniciado sesión.']);
    exit;
}

// Conectar con la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");

// Verificar conexión
if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit;
}

// Obtener el nombre de usuario actual
$usuario = $_SESSION['usuario'];

// Buscar los datos del atleta por su nombre de usuario
$sql = "SELECT nombre, apellido1, apellido2, correo, telefono FROM atletas WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $datos = $resultado->fetch_assoc();
    echo json_encode($datos);
} else {
    echo json_encode(['error' => 'No se encontraron los datos del atleta.']);
}

$stmt->close();
$conexion->close();
?>
