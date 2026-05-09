<?php
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $captcha = $_POST['captcha'];
    $captcha_result = $_POST['captcha_result'];

    if ($captcha != $captcha_result) {
        $mensaje = "El Captcha es incorrecto. Eres un robot?";
    } else if ($password === $confirm_password) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $check_email = "SELECT * FROM user WHERE Mail = '$correo'";
        $result = $conexion->query($check_email);

        if ($result->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            $sql = "INSERT INTO user (Name, Mail, Password, Verified, VerificationCode) VALUES ('$nombre', '$correo', '$password_hash', 0, '$verification_code')";

            if ($conexion->query($sql)) {
                $to = $correo;
                $subject = "Verifica tu cuenta de Entrenador - PokePimas";
                $message = "Hola $nombre,\n\nBienvenido a PokePimas.\n\nTu codigo secreto de verificacion es: $verification_code\n\nIntroducelo en nuestra web para completar el registro y comenzar tu aventura.\n\nAtentamente,\nEl Equipo de PokePimas (No responder a este correo).";
                $headers = "From: noreply@pokepimas.com\r\n";
                $headers .= "Reply-To: soporte@pokepimas.com\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                $correo_enviado = @mail($to, $subject, $message, $headers);

                if ($correo_enviado) {
                    header("Location: verificar.php?mail=" . urlencode($correo));
                } else {
                    header("Location: verificar.php?mail=" . urlencode($correo) . "&debug_code=" . urlencode($verification_code));
                }
                exit();
            } else {
                $mensaje = "Error: " . $conexion->error;
            }
        }
    } else {
        $mensaje = "Las contraseñas no coinciden.";
    }
}

$num1 = rand(1, 10);
$num2 = rand(1, 10);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - PokePimas Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Registro Nuevo Entrenador</h2>

        <?php if ($mensaje != "") {
            echo "<p style='color:red'>$mensaje</p>";
        } ?>

        <form action="registro.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Tu nombre completo" required>

            <label>Correo:</label>
            <input type="email" name="correo" placeholder="tu@email.com" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <label>Repetir Contraseña:</label>
            <input type="password" name="confirm_password" required>

            <div style="margin-bottom: 15px;">
                <label>Resuelve para verificar que eres humano: <?php echo $num1 . " + " . $num2; ?> =</label>
                <input type="number" name="captcha" required>
                <input type="hidden" name="captcha_result" value="<?php echo ($num1 + $num2); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Unirse a la Aventura</button>
        </form>

        <a href="../index.php">Volver al Login</a>
    </div>

</body>

</html>