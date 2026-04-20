<?php
session_start();
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_input = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password = $_POST['password'];

    // Permitir login con Name o Mail
    $sql = "SELECT * FROM user WHERE name = '$usuario_input' OR mail = '$usuario_input'";
    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verificamos si la cuenta está bloqueada temporalmente
        if (!empty($row['lockout_time'])) {
            $lockout_time = strtotime($row['lockout_time']);
            if (time() < $lockout_time) {
                $minutos_restantes = ceil(($lockout_time - time()) / 60);
                $mensaje = "Cuenta bloqueada por seguridad. Por favor, inténtelo de nuevo en " . $minutos_restantes . " minutos.";
            }
            else {
                // Si el tiempo de bloqueo ya expiró, reseteamos el contador
                $update_lockout = "UPDATE user SET failed_attempts = 0, lockout_time = NULL WHERE id = " . $row['id'];
                mysqli_query($conexion, $update_lockout);
                $row['failed_attempts'] = 0;
                $row['lockout_time'] = NULL;
            }
        }

        // Si no está bloqueado, verificamos la contraseña
        if ($mensaje === "") {
            // Soportamos hash seguro nuevo (password_verify) y también el antiguo para no romper cuentas
            $is_password_correct = false;
            $old_hash = substr(sha1($password), 0, 20);

            if (password_verify($password, $row['password'])) {
                $is_password_correct = true;
            }
            elseif ($old_hash === $row['password']) {
                $is_password_correct = true;
            // (Opcional) Re-hashear aquí la contraseña a formato seguro (BCRYPT) y guardar en DB
            }

            if ($is_password_correct) {
                // Resetear intentos fallidos al tener éxito
                if ($row['failed_attempts'] > 0) {
                    $reset_sql = "UPDATE user SET failed_attempts = 0, lockout_time = NULL WHERE id = " . $row['id'];
                    mysqli_query($conexion, $reset_sql);
                }

                if ($row['verified'] == 1) {
                    $_SESSION['usuario_id'] = $row['id'];
                    $_SESSION['nombre'] = $row['name'];
                    header("Location: inicio.php");
                    exit();
                }
                else {
                    $mensaje = "Disculpa, tu cuenta aún no está verificada. Revisa tu correo.";
                }
            }
            else {
                // Contraseña incorrecta, incrementamos el contador de intentos
                $intentos_fallidos = $row['failed_attempts'] + 1;

                if ($intentos_fallidos >= 3) {
                    // Bloquear por 15 minutos en el 3er intento fallido
                    $bloqueo_hasta = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    $query_update = "UPDATE user SET failed_attempts = $intentos_fallidos, lockout_time = '$bloqueo_hasta' WHERE id = " . $row['id'];
                    mysqli_query($conexion, $query_update);
                    $mensaje = "Demasiados intentos fallidos. Su cuenta ha sido bloqueada temporalmente por seguridad.";
                }
                else {
                    $query_update = "UPDATE user SET failed_attempts = $intentos_fallidos WHERE id = " . $row['id'];
                    mysqli_query($conexion, $query_update);
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
    <link rel="stylesheet" href="../style/style.css?v=12">
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

        <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        <br><br>
        <a href="inicio.php" style="color: #666; font-size: 0.9em;">Entrar sin registrarse</a>
    </div>

</body>

</html>
