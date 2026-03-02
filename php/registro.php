<?php
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Como la tabla user tiene password VARCHAR(20), password_hash (que genera 60 chars) se trunca 
        // y luego password_verify falla siempre. 
        // Creamos un hash y lo recortamos a 20 caracteres (o usamos texto plano, pero mejor esto).
        $password_hash = substr(sha1($password), 0, 20);

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

            $sql = "INSERT INTO user (id, name, mail, password, verified) VALUES ($siguiente_id, '$nombre', '$correo', '$password_hash', 0)";

            if (mysqli_query($conexion, $sql)) {
                // Generar token para verificación (usamos el mail y hash de contraseña guardado)
                $token = sha1($correo . $password_hash);
                // URL simulada
                $url_verificacion = "verificar.php?mail=" . urlencode($correo) . "&token=" . $token;

                // Como es localhost, simulamos el email con un Alert de JS
                echo "<script>
                    alert('Registro exitoso. Se ha ENVIADO un email a tu correo para verificar la cuenta.\\n\\n[SIMULACIÓN DE CORREO]\\nHaz click en este enlace (copiando la URL):\\nhttp://localhost/proyectoPIMAS/proyectoASIR/php/$url_verificacion');
                    window.location.href='index.php';
                </script>";
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
    <title>Registro - PokeNexus Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
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