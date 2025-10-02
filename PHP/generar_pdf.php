<?php
session_start();
require('fpdf/fpdf.php');

// Verifica sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login.html");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "dashboard_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

/* 1) Obtener id_atleta según el usuario */
$id_atleta = null;
$stmt = $conexion->prepare("SELECT id_atleta FROM atletas WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($id_atleta);
$stmt->fetch();
$stmt->close();

if (!$id_atleta) {
    die("No se encontró el atleta asociado al usuario.");
}

/* 2) Traer reservas desde la tabla correcta con alias */
$sql = "
SELECT 
  servicios      AS servicio,
  fecha_reserva  AS fecha,
  estado_reserva AS estado
FROM reservaciones
WHERE id_atleta = ?
ORDER BY fecha_reserva DESC
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_atleta);
$stmt->execute();
$resultado = $stmt->get_result();

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Historial de Reservaciones - UIA Sport GYM', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Servicio', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(40, 10, 'Estado', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
if ($resultado && $resultado->num_rows > 0) {
    while ($reserva = $resultado->fetch_assoc()) {
        $pdf->Cell(70, 10, $reserva['servicio'], 1);
        $pdf->Cell(40, 10, $reserva['fecha'], 1);
        $pdf->Cell(40, 10, $reserva['estado'], 1); // usa el valor tal cual (p.ej. 'Pendiente')
        $pdf->Ln();
    }
} else {
    $pdf->Cell(150, 10, 'No hay reservaciones registradas.', 1, 1, 'C');
}

$pdf->Output();

$stmt->close();
$conexion->close();
?>