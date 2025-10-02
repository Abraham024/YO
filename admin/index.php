<?php
// Incluye el archivo de encabezado, que ya maneja la sesión y la conexión
require_once('includes/header.php');
?>
<p>Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?>. Aquí puedes gestionar el gimnasio.</p>
<?php

require_once('includes/footer.php');
?>