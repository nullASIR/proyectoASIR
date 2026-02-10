<?php
session_start();
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_input = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password = $_POST['password'];

    // Permitir login con Nombre o Correo
    $sql = "SELECT * FROM usuarios WHERE nombre = '$usuario_input' OR correo = '$usuario_input'";
    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            header("Location: inicio.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Acceso Entrenador Pokemon</h2>

        <?php if ($mensaje != "") {
            echo "<p style='color:red'>$mensaje</p>";
        } ?>

        <form action="index.php" method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" placeholder="Escribe tu usuario o correo" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Tu contraseña" required>

            <button type="submit">Entrar</button>
        </form>

        <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        <br><br>
        <a href="inicio.php" style="color: #666; font-size: 0.9em;">Entrar sin registrarse</a>
    </div>

</body>

</html>