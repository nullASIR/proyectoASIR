<?php
include 'database.php';

$mensaje = "";
$exito = false;
$token_valido = false;
$correo_usuario = "";

if (isset($_GET['token'])) {
    $token = substr($conexion->quote($_GET['token']), 1, -1);
    
    // Verificar si el token es válido y no ha expirado
    $sql = "SELECT Mail FROM user WHERE ResetToken = '$token' AND ResetExpires > NOW()";
    $result = $conexion->query($sql);
    
    if ($result->rowCount() === 1) {
        $token_valido = true;
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $correo_usuario = $row['Mail'];
    } else {
        $mensaje = "El enlace de recuperación es inválido o ha expirado.";
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = substr($conexion->quote($_POST['token']), 1, -1);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validar token de nuevo por seguridad
    $sql = "SELECT Mail FROM user WHERE ResetToken = '$token' AND ResetExpires > NOW()";
    $result = $conexion->query($sql);
    
    if ($result->rowCount() === 1) {
        if ($password === $confirm_password) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $correo = $row['Mail'];
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Actualizar contraseña y resetear token/intentos
            $update_sql = "UPDATE user SET Password = '$password_hash', ResetToken = NULL, ResetExpires = NULL, FailedAttempts = 0, LockoutTime = NULL WHERE Mail = '$correo'";
            
            if ($conexion->query($update_sql)) {
                $mensaje = "¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.";
                $exito = true;
            } else {
                $mensaje = "Hubo un error al actualizar la contraseña. Inténtalo más tarde.";
                $token_valido = true; // Para mostrar el formulario de nuevo
            }
        } else {
            $mensaje = "Las contraseñas no coinciden. Inténtalo de nuevo.";
            $token_valido = true; // Para mostrar el formulario de nuevo
        }
    } else {
        $mensaje = "El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.";
    }
} else {
    $mensaje = "No se proporcionó ningún token válido.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - PokePimas Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>
    <div class="contenedor-auth">
        <h2>Nueva Contraseña</h2>

        <?php if ($exito): ?>
            <div class="msg-success" style="color: green; border: 1px solid green; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <strong>Correcto:</strong> <?php echo $mensaje; ?>
            </div>
            
            <a href="../index.php" class="btn btn-primary btn-block" style="text-align: center;">Iniciar Sesión</a>
        <?php else: ?>
            <?php if ($mensaje != ""): ?>
                <div class="msg-error" style="color: red; border: 1px solid red; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php if ($token_valido): ?>
                <p style="text-align: center; color: var(--text-secondary); margin-bottom: 20px;">
                    Por favor, escribe tu nueva contraseña y confírmala.
                </p>

                <form action="reset_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <label>Nueva Contraseña:</label>
                    <input type="password" name="password" required minlength="6">

                    <label>Confirmar Contraseña:</label>
                    <input type="password" name="confirm_password" required minlength="6">

                    <button type="submit" class="btn btn-primary btn-block">Guardar Contraseña</button>
                </form>
            <?php else: ?>
                <?php if ($mensaje == ""): ?>
                    <p style="text-align: center;">Enlace no válido.</p>
                <?php endif; ?>
                <a href="olvide_contrasena.php" class="btn btn-outline btn-block" style="text-align: center; margin-top: 15px;">Solicitar nuevo enlace</a>
            <?php endif; ?>
            
            <a href="../index.php" style="display: block; text-align: center; margin-top: 15px;">Volver al Inicio</a>
        <?php endif; ?>
    </div>
</body>

</html>
