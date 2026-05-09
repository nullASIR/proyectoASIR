<?php
include 'database.php';

$mensaje = "";
$exito = false;
$mail = isset($_GET['mail']) ? htmlspecialchars($_GET['mail']) : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail_post = mysqli_real_escape_string($conexion, $_POST['mail']);
    $codigo_ingresado = mysqli_real_escape_string($conexion, $_POST['codigo']);

    $sql = "SELECT Id, Verified, VerificationCode FROM user WHERE Mail = '$mail_post'";
    $result = $conexion->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($row['Verified'] == 1) {
            $mensaje = "Tu cuenta ya estaba verificada anteriormente.";
            $exito = true;
        } else {
            if ($codigo_ingresado === $row['VerificationCode']) {
                $update_sql = "UPDATE user SET Verified = 1, VerificationCode = NULL WHERE Mail = '$mail_post'";
                if ($conexion->query($update_sql)) {
                    $mensaje = "¡Cuenta verificada con éxito! Ya puedes entrar como Entrenador.";
                    $exito = true;
                } else {
                    $mensaje = "Error en el servidor al intentar validar tu cuenta.";
                }
            } else {
                $mensaje = "El código ingresado es incorrecto. Vuelve a intentarlo.";
                $mail = $mail_post;
            }
        }
    } else {
        $mensaje = "No existe ninguna cuenta asociada a este correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación - PokePimas Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
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
            <div class="msg-success"
                style="color: green; border: 1px solid green; padding: 15px; border-radius: 8px; text-align: center;">
                <strong>Correcto:</strong> <?php echo $mensaje; ?>
            </div>
            <br>
            <a href="../index.php" class="btn btn-primary btn-block" style="text-align: center;">
                Ir a Iniciar Sesión
            </a>
            <?php
        else: ?>

            <?php if (isset($_GET['debug_code'])): ?>
                <div class="msg-warning"
                    style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <strong>⚠️ MODO DESARROLLO (Servidor Local)</strong><br><br>
                    Tu código secreto autogenerado es: <strong
                        style="font-size: 1.5rem; letter-spacing: 0.2rem; display: block; margin-top: 10px;"><?php echo htmlspecialchars($_GET['debug_code']); ?></strong>
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
                <input type="text" name="codigo" class="code-input" placeholder="000000" maxlength="6" pattern="\d{6}"
                    required>

                <button type="submit" class="btn btn-primary btn-block">Verificar y Entrar</button>
            </form>

            <a href="../index.php" style="display: block; text-align: center; margin-top: 15px;">Volver al Inicio</a>
            <?php
        endif; ?>
    </div>

</body>

</html>