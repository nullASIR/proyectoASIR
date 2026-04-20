<?php
include 'database.php';

$mensaje = "";
$exito = false;
$mail = isset($_GET['mail']) ? htmlspecialchars($_GET['mail']) : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail_post = mysqli_real_escape_string($conexion, $_POST['mail']);
    $codigo_ingresado = mysqli_real_escape_string($conexion, $_POST['codigo']);

    $sql = "SELECT id, verified, verification_code FROM user WHERE mail = '$mail_post'";
    $result = mysqli_query($conexion, $sql);

    // Verificar si el correo existe
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Comprobar si ya está verificado
        if ($row['verified'] == 1) {
            $mensaje = "Tu cuenta ya estaba verificada anteriormente.";
            $exito = true;
        }
        else {
            // Validar código
            if ($codigo_ingresado === $row['verification_code']) {
                // Token correcto, verificamos la cuenta y eliminamos el código por seguridad
                $update_sql = "UPDATE user SET verified = 1, verification_code = NULL WHERE mail = '$mail_post'";
                if (mysqli_query($conexion, $update_sql)) {
                    $mensaje = "¡Cuenta verificada con éxito! Ya puedes entrar como Entrenador.";
                    $exito = true;
                }
                else {
                    $mensaje = "Error en el servidor al intentar validar tu cuenta.";
                }
            }
            else {
                $mensaje = "El código ingresado es incorrecto. Vuelve a intentarlo.";
                $mail = $mail_post; // Mantener el correo en el form si falla
            }
        }
    }
    else {
        $mensaje = "No existe ninguna cuenta asociada a este correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación - PokePimas Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
    <style>
        .code-input {
            text-align: center;
            font-size: 2rem !important;
            letter-spacing: 0.5rem;
            font-family: 'Montserrat', monospace;
            font-weight: 800;
        }
    </style>
</head>

<body>

    <div class="contenedor-auth">
        <h2>Verificación de Cuenta</h2>
        
        <?php if ($exito): ?>
            <div class="msg-success" style="color: green; border: 1px solid green; padding: 15px; border-radius: 8px; text-align: center;">
                <strong>Correcto:</strong> <?php echo $mensaje; ?>
            </div>
            <br>
            <a href="index.php" class="btn btn-primary btn-block" style="text-align: center;">
                Ir a Iniciar Sesión
            </a>
        <?php
else: ?>
            
            <?php if (isset($_GET['debug_code'])): ?>
                <div class="msg-warning" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <strong>⚠️ MODO DESARROLLO (Servidor Local)</strong><br><br>
                    Como no hay servidor SMTP en XAMPP, simulo el envío del correo de verificación para que puedas probar el registro. <br><br>
                    Tu código secreto autogenerado es: <strong style="font-size: 1.5rem; letter-spacing: 0.2rem; display: block; margin-top: 10px;"><?php echo htmlspecialchars($_GET['debug_code']); ?></strong>
                </div>
            <?php
    endif; ?>

            <?php if ($mensaje != ""): ?>
                <div class="msg-error" style="color: red; padding-bottom: 15px; text-align: center;">
                    <strong>Atención:</strong> <?php echo $mensaje; ?>
                </div>
            <?php
    else: ?>
                <p style="text-align: center; color: var(--text-secondary); margin-bottom: 20px;">
                    Te hemos enviado un correo electrónico con un código de 6 dígitos. Escríbelo abajo para continuar tu viaje.
                </p>
            <?php
    endif; ?>

            <form action="verificar.php" method="POST">
                
                <input type="hidden" name="mail" value="<?php echo htmlspecialchars($mail); ?>">
                
                <label style="text-align: center; display: block;">Código Secreto:</label>
                <input type="text" name="codigo" class="code-input" placeholder="000000" maxlength="6" pattern="\d{6}" required>

                <button type="submit" class="btn btn-primary btn-block">Verificar y Entrar</button>
            </form>
            
            <a href="index.php" style="display: block; text-align: center; margin-top: 15px;">Volver al Inicio</a>
        <?php
endif; ?>
    </div>

</body>

</html>

