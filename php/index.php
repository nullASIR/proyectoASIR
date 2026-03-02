<?php
session_start();
include 'database.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_input = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password = $_POST['password'];

    // Permitir login con Name o Mail
    $sql = "SELECT * FROM user WHERE name = '$usuario_input' OR mail = '$usuario_input'";
    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verificamos comparando con el hash que usaremos (ej: sha1 para que quepa en 20 caracteres)
        // ya que la base de datos truncaba el password_hash
        $hashed_password = substr(sha1($password), 0, 20);

        if ($hashed_password === $row['password']) {
            if ($row['verified'] == 1) {
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['nombre'] = $row['name']; // Seguimos usando la clave de sesión 'nombre' pero viene del campo 'name' de la BD
                header("Location: inicio.php");
                exit();
            }
            else {
                $mensaje = "Disculpa, tu cuenta aún no está verificada. Revisa tu correo.";
            }
        }
        else {
            $mensaje = "Contraseña incorrecta.";
        }
    }
    else {
        $mensaje = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - PokeNexus Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <div class="contenedor-auth">
        <h2>Acceso Entrenador Pokemon</h2>

        <?php if ($mensaje != "") {
    echo "<p style='color:red'>$mensaje</p>";
}?>

        <form action="index.php" method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" placeholder="Escribe tu usuario o correo" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Tu contraseña" required>

            <button type="submit" class="btn btn-primary btn-block">Entrar Mágicamente</button>
        </form>

        <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        <br><br>
        <a href="inicio.php" style="color: #666; font-size: 0.9em;">Entrar sin registrarse</a>
    </div>

</body>

</html>