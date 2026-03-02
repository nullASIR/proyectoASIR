<?php
include 'database.php';

$mensaje = "";
$exito = false;

if (isset($_GET['mail']) && isset($_GET['token'])) {
    $mail = mysqli_real_escape_string($conexion, $_GET['mail']);
    $token = $_GET['token'];

    $sql = "SELECT id, password, verified FROM user WHERE mail = '$mail'";
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
            // Re-generar el token esperado usando la DB
            $expected_token = sha1($mail . $row['password']);

            // Validar token
            if ($token === $expected_token) {
                // Token correcto, verificamos la cuenta
                $update_sql = "UPDATE user SET verified = 1 WHERE mail = '$mail'";
                if (mysqli_query($conexion, $update_sql)) {
                    $mensaje = "¡Cuenta verificada con éxito! Ya puedes entrar como Entrenador.";
                    $exito = true;
                }
                else {
                    $mensaje = "Error en el servidor al intentar validar tu cuenta.";
                }
            }
            else {
                $mensaje = "El enlace proporcionado no es válido o ha sido modificado.";
            }
        }
    }
    else {
        $mensaje = "No existe ninguna cuenta asociada a este correo.";
    }
}
else {
    $mensaje = "Enlace inválido. Faltan parámetros en la URL.";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación - PokeNexus Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Verificación de Correo</h2>
        
        <?php if ($exito): ?>
            <div class="msg-success">
                <strong>Correcto:</strong> <?php echo $mensaje; ?>
            </div>
            <br>
            <a class="btn-primary" href="index.php" style="display:inline-block; padding:10px 20px; background:var(--accent-blue); color:white; border-radius:8px;">
                Ir a Iniciar Sesión
            </a>
        <?php
else: ?>
            <div class="msg-error">
                <strong>Error:</strong> <?php echo $mensaje; ?>
            </div>
            <br>
            <a href="index.php">Volver al Inicio</a>
        <?php
endif; ?>
    </div>

</body>

</html>
