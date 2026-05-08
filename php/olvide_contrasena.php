<?php
include 'database.php';

$mensaje = "";
$exito = false;
$debug_link = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);

    $sql = "SELECT Id, Name FROM user WHERE Mail = '$correo'";
    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['Name'];
        
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $update_sql = "UPDATE user SET ResetToken = '$token', ResetExpires = '$expira' WHERE Mail = '$correo'";
        
        if (mysqli_query($conexion, $update_sql)) {
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . urlencode($token);
            
            $to = $correo;
            $subject = "Recuperacion de Contrasena - PokePimas";
            $message = "Hola $nombre,\n\nHas solicitado restablecer tu contrasena.\n\nHaz clic en el siguiente enlace o copialo en tu navegador:\n$reset_link\n\nEste enlace expira en 1 hora.\n\nSi no fuiste tu, ignora este correo.\n\nAtentamente,\nEl Equipo de PokePimas.";
            $headers = "From: noreply@pokepimas.com\r\n";
            $headers .= "Reply-To: soporte@pokepimas.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            $correo_enviado = @mail($to, $subject, $message, $headers);

            $mensaje = "Si el correo está registrado, te hemos enviado un enlace para restablecer tu contraseña.";
            $exito = true;
            
            if (!$correo_enviado) {
                // Modo desarrollo
                $debug_link = $reset_link;
            }
        } else {
            $mensaje = "Error al procesar la solicitud.";
        }
    } else {
        // Por seguridad, damos el mismo mensaje para no revelar qué correos existen
        $mensaje = "Si el correo está registrado, te hemos enviado un enlace para restablecer tu contraseña.";
        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - PokePimas Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>
    <div class="contenedor-auth">
        <h2>Recuperar Contraseña</h2>

        <?php if ($exito): ?>
            <div class="msg-success" style="color: green; border: 1px solid green; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                <strong>Aviso:</strong> <?php echo $mensaje; ?>
            </div>
            
            <?php if ($debug_link): ?>
                <div class="msg-warning" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; overflow-wrap: anywhere;">
                    <strong>⚠️ MODO DESARROLLO (Servidor Local)</strong><br><br>
                    Como no hay servidor SMTP en XAMPP, simulo el envío del correo de recuperación. <br><br>
                    Enlace de recuperación:<br> <a href="<?php echo $debug_link; ?>"><?php echo $debug_link; ?></a>
                </div>
            <?php endif; ?>
            
            <a href="../index.php" class="btn btn-outline btn-block" style="text-align: center;">Volver al Login</a>
        <?php else: ?>
            <?php if ($mensaje != ""): ?>
                <p style='color:red; text-align: center;'><?php echo $mensaje; ?></p>
            <?php endif; ?>
            
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 20px;">
                Introduce tu correo electrónico y te enviaremos las instrucciones para crear una nueva contraseña.
            </p>

            <form action="olvide_contrasena.php" method="POST">
                <label>Correo Electrónico:</label>
                <input type="email" name="correo" placeholder="tu@email.com" required>

                <button type="submit" class="btn btn-primary btn-block">Enviar Enlace</button>
            </form>

            <a href="../index.php" style="display: block; text-align: center; margin-top: 15px;">Volver al Inicio de Sesión</a>
        <?php endif; ?>
    </div>
</body>

</html>
