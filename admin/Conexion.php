<?php
$servername= "localhost";
$database= "dashboard_db";
$username= "root";
$password= "";

//crear el string de conexion a la base
$conn= mysqli_connect($servername,$username, $password, $database);
//revisar si conecta
if (!$conn)
    die("Conexion fallo:".mysqli_connect_error());
else
    echo "Conectamos con exito";

//cerramos conexion
//mysqli_close($conn);

?>