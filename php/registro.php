<?php
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Creamos un hash seguro con BCRYPT
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Verificar si el correo ya existe
        $check_email = "SELECT * FROM user WHERE mail = '$correo'";
        $result = mysqli_query($conexion, $check_email);

        if (mysqli_num_rows($result) > 0) {
            $mensaje = "El correo ya está registrado.";
        }
        else {
            // Calcular el siguiente ID manualmente
            $resultado_max_id = mysqli_query($conexion, "SELECT MAX(id) AS max_id FROM user");
            $fila_max_id = mysqli_fetch_assoc($resultado_max_id);
            $siguiente_id = ($fila_max_id['max_id'] !== null) ? $fila_max_id['max_id'] + 1 : 1;

            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            $sql = "INSERT INTO user (id, name, mail, password, verified, verification_code) VALUES ($siguiente_id, '$nombre', '$correo', '$password_hash', 0, '$verification_code')";

            if (mysqli_query($conexion, $sql)) {
                // Generar token para verificación real con mail()
                $to = $correo;
                $subject = "Verifica tu cuenta de Entrenador - PokePimas";
                $message = "Hola $nombre,\n\nBienvenido a PokePimas.\n\nTu codigo secreto de verificacion es: $verification_code\n\nIntroducelo en nuestra web para completar el registro y comenzar tu aventura.\n\nAtentamente,\nEl Equipo de PokePimas (No responder a este correo).";
                $headers = "From: noreply@pokepimas.com\r\n";
                $headers .= "Reply-To: soporte@pokepimas.com\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                // Enviar correo real (requiere tener servidor SMTP / Sendmail configurado en el servidor/PHP.ini)
                $correo_enviado = @mail($to, $subject, $message, $headers);

                if ($correo_enviado) {
                    // Si se envió correctamente, redirigimos limpiamente
                    header("Location: verificar.php?mail=" . urlencode($correo));
                }
                else {
                    // Si falla el envío (muy común en XAMPP sin SMTP), mandamos el código por URL para el Modo Desarrollador
                    header("Location: verificar.php?mail=" . urlencode($correo) . "&debug_code=" . urlencode($verification_code));
                }
                exit();
            }
            else {
                $mensaje = "Error: " . mysqli_error($conexion);
            }
        }
    }
    else {
        $mensaje = "Las contraseñas no coinciden.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - PokePimas Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Registro Nuevo Entrenador</h2>

        <?php if ($mensaje != "") {
    echo "<p style='color:red'>$mensaje</p>";
}?>

        <form action="registro.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Tu nombre completo" required>

            <label>Correo:</label>
            <input type="email" name="correo" placeholder="tu@email.com" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <label>Repetir Contraseña:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" class="btn btn-primary btn-block">Unirse a la Aventura</button>
        </form>

        <a href="index.php">Volver al Login</a>
    </div>

</body>

</html>
