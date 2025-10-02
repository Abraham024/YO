<?php
$servername = "localhost";
$database = "dashboard_db";
$username = "root";
$password = "";

// Crear el objeto de conexión
$conexion = new mysqli($servername, $username, $password, $database);

// Revisar si conecta
if ($conexion->connect_error) {
    die("Conexión falló: " . $conexion->connect_error);
} else {
    // Puedes comentar esta línea en producción
    // echo "Conectamos con éxito";
}

// **Importante:** Se elimina la línea para cerrar la conexión aquí.
// La conexión debe permanecer abierta para que los otros scripts la utilicen.
// Se cerrará al final de cada página en footer.php.
?>