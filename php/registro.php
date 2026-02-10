<?php
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Verificar si el correo ya existe
        $check_email = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $result = mysqli_query($conexion, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
             $mensaje = "El correo ya está registrado.";
        } else {
            $sql = "INSERT INTO usuarios (nombre, correo, password) VALUES ('$nombre', '$correo', '$password_hash')";
            
            if (mysqli_query($conexion, $sql)) {
                echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href='index.php';</script>";
            } else {
                $mensaje = "Error: " . mysqli_error($conexion);
            }
        }
    } else {
        $mensaje = "Las contraseñas no coinciden.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Registro Nuevo Entrenador</h2>

        <?php if($mensaje != "") { echo "<p style='color:red'>$mensaje</p>"; } ?>

        <form action="registro.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Tu nombre completo" required>

            <label>Correo:</label>
            <input type="email" name="correo" placeholder="tu@email.com" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <label>Repetir Contraseña:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Registrarse</button>
        </form>

        <a href="index.php">Volver al Login</a>
    </div>

</body>

</html>