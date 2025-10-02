<?php
session_start();
require_once('../conexion.php'); // Debe definir $conexion (mysqli)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Entradas sanitizadas
    $usuario    = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

    // 1) Traer el usuario (sin filtrar por rol aquí)
    $stmt = $conexion->prepare("SELECT id_atleta, nombre, contrasena, rol FROM atletas WHERE usuario = ? LIMIT 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 2) Verificar ROL (case-insensitive y admitiendo 'admin' o 'administrador')
        $rol = strtolower($user['rol'] ?? '');
        $esAdmin = ($rol === 'administrador' || $rol === 'admin');

        if (!$esAdmin) {
            $error = "Usuario sin permisos de administrador.";
        } else {
            // 3) Verificar contraseña
            $hash = $user['contrasena'];

            $okPass = false;
            // Si parece hash (bcrypt/argon), usar password_verify
            if (preg_match('/^\$2y\$|\$2a\$|\$argon2id\$|\$argon2i\$/', $hash)) {
                $okPass = password_verify($contrasena, $hash);
            } else {
                // Fallback a texto plano (si tu BD guarda contraseñas sin hash)
                $okPass = hash_equals($hash, $contrasena);
            }

            if ($okPass) {
                $_SESSION['id_atleta'] = $user['id_atleta'];
                $_SESSION['nombre']    = $user['nombre'];
                $_SESSION['rol']       = $user['rol'];

                header('Location: index.php');
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login de Administrador</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background-color: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 320px; }
        .login-container h2 { text-align: center; color: #333; margin-top: 0; }
        .login-container form { display: flex; flex-direction: column; }
        .login-container input[type="text"], .login-container input[type="password"] { margin-bottom: 1em; padding: 0.8em; border: 1px solid #ccc; border-radius: 4px; }
        .login-container button { padding: 0.8em; background-color: #5cb85c; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .login-container .error { color: red; text-align: center; margin-bottom: 1em; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Acceso Administrativo</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST" autocomplete="off">
            <input type="text" name="usuario" placeholder="Usuario" required />
            <input type="password" name="contrasena" placeholder="Contraseña" required />
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>