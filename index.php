<?php
session_start();
include 'php/database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_input = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password = $_POST['password'];

    // Permitir login con Name o Mail
    $sql = "SELECT * FROM user WHERE Name = '$usuario_input' OR Mail = '$usuario_input'";
    $result = $conexion->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verificamos si la cuenta está bloqueada temporalmente
        if (!empty($row['LockoutTime'])) {
            $lockout_time = strtotime($row['LockoutTime']);
            if (time() < $lockout_time) {
                $minutos_restantes = ceil(($lockout_time - time()) / 60);
                $mensaje = "Cuenta bloqueada por seguridad. Por favor, inténtelo de nuevo en " . $minutos_restantes . " minutos.";
            }
            else {
                // Si el tiempo de bloqueo ya expiró, reseteamos el contador
                $update_lockout = "UPDATE user SET FailedAttempts = 0, LockoutTime = NULL WHERE Id = " . $row['Id'];
                $conexion->query($update_lockout);
                $row['FailedAttempts'] = 0;
                $row['LockoutTime'] = NULL;
            }
        }

        // Si no está bloqueado, verificamos la contraseña
        if ($mensaje === "") {
            // Soportamos hash seguro nuevo (password_verify) y también el antiguo para no romper cuentas
            $is_password_correct = false;
            $old_hash = substr(sha1($password), 0, 20);

            if (password_verify($password, $row['Password'])) {
                $is_password_correct = true;
            }
            elseif ($old_hash === $row['Password']) {
                $is_password_correct = true;
            // (Opcional) Re-hashear aquí la contraseña a formato seguro (BCRYPT) y guardar en DB
            }

            if ($is_password_correct) {
                // Resetear intentos fallidos al tener éxito
                if ($row['FailedAttempts'] > 0) {
                    $reset_sql = "UPDATE user SET FailedAttempts = 0, LockoutTime = NULL WHERE Id = " . $row['Id'];
                    $conexion->query($reset_sql);
                }

                if ($row['Verified'] == 1) {
                    $_SESSION['usuario_id'] = $row['Id'];
                    $_SESSION['nombre'] = $row['Name'];
                    $_SESSION['is_admin'] = isset($row['IsAdmin']) ? $row['IsAdmin'] : false;
                    header("Location: php/inicio.php");
                    exit();
                }
                else {
                    $mensaje = "Disculpa, tu cuenta aún no está verificada. Revisa tu correo.";
                }
            }
            else {
                // Contraseña incorrecta, incrementamos el contador de intentos
                $intentos_fallidos = $row['FailedAttempts'] + 1;

                if ($intentos_fallidos >= 3) {
                    // Bloquear por 15 minutos en el 3er intento fallido
                    $bloqueo_hasta = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    $query_update = "UPDATE user SET FailedAttempts = $intentos_fallidos, LockoutTime = '$bloqueo_hasta' WHERE Id = " . $row['Id'];
                    $conexion->query($query_update);
                    $mensaje = "Demasiados intentos fallidos. Su cuenta ha sido bloqueada temporalmente por seguridad.";
                }
                else {
                    $query_update = "UPDATE user SET FailedAttempts = $intentos_fallidos WHERE Id = " . $row['Id'];
                    $conexion->query($query_update);
                    $intentos_restantes = 3 - $intentos_fallidos;
                    $mensaje = "Contraseña incorrecta. Te quedan $intentos_restantes intentos antes del bloqueo.";
                }
            }
        }
    }
    else {
        $mensaje = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - PokePimas Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css?v=12">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Acceso Entrenador</h2>

        <?php if ($mensaje != "") {
    echo "<p style='color:red'>$mensaje</p>";
}?>

        <form action="index.php" method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" placeholder="Escribe tu usuario o correo" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Tu contraseña" required>

            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>

        <a href="php/registro.php">¿No tienes cuenta? Regístrate aquí</a>
        <br><br>
        <a href="php/olvide_contrasena.php">¿Olvidaste tu contraseña?</a>
        <br><br>
        <a href="php/inicio.php" style="color: #666; font-size: 0.9em;">Entrar sin registrarse</a>
    </div>

</body>

</html>
