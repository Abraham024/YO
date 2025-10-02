<?php
// Cierra la conexión a la base de datos
if (isset($conexion) && $conexion instanceof mysqli) {
    $conexion->close();
}
?>
    </div> </body>
</html>