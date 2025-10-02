<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login.html");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID no especificado.";
    exit();
}

$id = (int)$_GET['id'];  // id_reservacion

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 1) Obtener id_atleta a partir del usuario en sesión
$usuario = $_SESSION['usuario'];
$id_atleta = null;

$stmt = $conexion->prepare("SELECT id_atleta FROM atletas WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($id_atleta);
$stmt->fetch();
$stmt->close();

if (!$id_atleta) {
    echo "<script>alert('No se encontró el atleta asociado al usuario.'); window.location.href = 'historial_reservas.php';</script>";
    $conexion->close();
    exit();
}

// 2) Verifica que la reservación pertenece al usuario y está pendiente
$sqlVer = "SELECT 1
           FROM reservaciones
           WHERE id_reservacion = ? AND id_atleta = ? AND LOWER(estado_reserva) = 'pendiente'
           LIMIT 1";
$verificar = $conexion->prepare($sqlVer);
$verificar->bind_param("ii", $id, $id_atleta);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    $verificar->close();

    // 3) Cancelar: mismo comportamiento que tenías (DELETE).
    //    Si prefieres marcar estado, cambia por:
    //    UPDATE reservaciones SET estado_reserva = 'Rechazada' WHERE id_reservacion = ? AND id_atleta = ?
    $del = $conexion->prepare("DELETE FROM reservaciones WHERE id_reservacion = ? AND id_atleta = ?");
    $del->bind_param("ii", $id, $id_atleta);

    if ($del->execute()) {
        echo "<script>alert('Reservación cancelada exitosamente.'); window.location.href = 'historial_reservas.php';</script>";
    } else {
        echo "<script>alert('No se pudo cancelar la reservación.'); window.location.href = 'historial_reservas.php';</script>";
    }
    $del->close();
} else {
    $verificar->close();
    echo "<script>alert('No se pudo cancelar la reservación.'); window.location.href = 'historial_reservas.php';</script>";
}

$conexion->close();
?>