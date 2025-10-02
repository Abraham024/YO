<?php
require_once('../conexion.php'); 

// Facturar
if (isset($_GET['accion']) && $_GET['accion'] === 'facturar' && isset($_GET['id'])) {
    $id_reservacion = (int)$_GET['id'];
    $stmt = $conexion->prepare("UPDATE reservaciones SET estado_reserva = 'Facturada' WHERE id_reservacion = ?");
    $stmt->bind_param("i", $id_reservacion);
    $stmt->execute();
    $stmt->close();
    header('Location: reservaciones.php');
    exit;
}

require_once('includes/header.php');
?>

<h2>Reservaciones Registradas</h2>
<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Atleta</th>
            <th>Zona</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
    
        $sql = "SELECT 
                    r.id_reservacion, 
                    a.nombre AS nombre_atleta, 
                    z.nombre AS servicios,        -- alias conservado para no cambiar el render
                    r.fecha_reserva, 
                    r.estado_reserva
                FROM reservaciones r
                JOIN atletas a ON r.id_atleta = a.id_atleta
                LEFT JOIN zonas_deportivas z ON r.servicios = z.nombre   -- aquí el fix
                ORDER BY r.fecha_reserva DESC";

        $result = $conexion->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . (int)$row['id_reservacion'] . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre_atleta']) . "</td>";
                echo "<td>" . htmlspecialchars($row['servicios']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_reserva']) . "</td>";
                echo "<td>" . htmlspecialchars($row['estado_reserva']) . "</td>";
                echo "<td>";
        
                if (strtolower($row['estado_reserva']) === 'pendiente') {
                    echo "<a href='reservaciones.php?accion=facturar&id=" . (int)$row['id_reservacion'] . "' onclick='return confirm(\"¿Desea facturar esta reservación?\")'>Facturar</a>";
                } else {
                    echo "";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay reservaciones registradas.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once('includes/footer.php'); ?>