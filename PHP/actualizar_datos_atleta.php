<?php
session_start();

// Verificar si el atleta ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No se ha iniciado sesión.']);
    exit;
}

// Leer los datos JSON enviados desde el frontend
$datos = json_decode(file_get_contents("php://input"), true);

// Validar datos recibidos
if (!$datos || !isset($datos['nombre'], $datos['apellido1'], $datos['apellido2'], $datos['correo'], $datos['telefono'])) {
    echo json_encode(['error' => 'Faltan datos por enviar.']);
    exit;
}

// Conectar con la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");

// Verificar conexión
if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit;
}

// Usuario actual
$usuario = $_SESSION['usuario'];

// Preparar y ejecutar la actualización
$sql = "UPDATE atletas 
        SET nombre = ?, apellido1 = ?, apellido2 = ?, correo = ?, telefono = ?
        WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssssss",
    $datos['nombre'],
    $datos['apellido1'],
    $datos['apellido2'],
    $datos['correo'],
    $datos['telefono'],
    $usuario
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'No se pudieron actualizar los datos.']);
}

$stmt->close();
$conexion->close();
?>
