<?php
session_start();

// Verifica si el atleta ha iniciado sesión
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

/* 1) Obtener id_atleta a partir del usuario (tabla: atletas) */
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

/* 2) Consultar reservaciones del atleta en la tabla correcta y con nombres reales.
      Alias para que el resto del HTML siga igual: id, servicio, fecha, estado */
$sql = "
SELECT 
  id_reservacion   AS id,
  servicios        AS servicio,
  fecha_reserva    AS fecha,
  estado_reserva   AS estado
FROM reservaciones
WHERE id_atleta = ?
ORDER BY fecha_reserva DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_atleta);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Reservaciones</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #eef2f7; padding: 30px; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ccc; }
        th { background-color: #DEA41E; color: white; }
        .btn-cancelar { padding: 6px 12px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-cancelar:hover { background-color: #c0392b; }
        .btn-pdf { margin-top: 20px; display: block; background-color: #10b981; color: white; padding: 12px 20px; text-align: center; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; width: fit-content; }
        .btn-pdf:hover { background-color: #0f766e; }
    </style>
    <script>
        function confirmarCancelacion(id) {
            if (confirm("¿Estás seguro de que deseas cancelar esta reservación?")) {
                window.location.href = "cancelar_reserva.php?id=" + id;
            }
        }
    </script>
</head>
<body>

<h2>Historial de Reservaciones</h2>

<table>
    <tr>
        <th>Servicio</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Días Restantes</th>
        <th>Acción</th>
    </tr>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($reserva = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($reserva['servicio']) ?></td>
                <td><?= htmlspecialchars($reserva['fecha']) ?></td>
                <td><?= htmlspecialchars($reserva['estado']) ?></td>
                <td>
                    <?php
                    // Por si el ENUM está capitalizado ('Pendiente'), comparamos en minúsculas
                    if (strtolower($reserva['estado']) === 'pendiente') {
                        $hoy = new DateTime();
                        $fechaReserva = new DateTime($reserva['fecha']);
                        $diferencia = (int)$hoy->diff($fechaReserva)->format('%r%a');
                        echo ($diferencia >= 0) ? $diferencia . " días" : "Vencida";
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
                <td>
                    <?php if (strtolower($reserva['estado']) === 'pendiente'): ?>
                        <button class="btn-cancelar" onclick="confirmarCancelacion(<?= (int)$reserva['id'] ?>)">Cancelar</button>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No hay reservaciones registradas.</td>
        </tr>
    <?php endif; ?>
</table>

<a class="btn-pdf" href="generar_pdf.php" target="_blank">Descargar en PDF</a>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>