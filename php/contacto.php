<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contacto - PokeTienda</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="logo">
            POKETIENDA
        </div>

        <div class="user-options">
            <a href="inicio.php">Inicio</a>
            <a href="cartas.php">Cartas</a>
            <a href="productos.php">Productos</a>
            <a href="contacto.php" style="color: #cc0000;">Contacto</a>
            <a href="ver_carrito.php">Carrito</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php">Salir</a>
            <?php else: ?>
                <a href="index.php">Iniciar Sesión</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">

        <h2 style="text-align: center; color: #fff;">Contáctanos</h2>
        <p style="text-align: center; color: #ccc;">¿Tienes dudas sobre alguna carta? ¡Escríbenos!</p>

        <form class="form-contacto">
            <label>Tu Nombre:</label>
            <input type="text" placeholder="Entrenador Pokemon">

            <label>Tu Email:</label>
            <input type="email" placeholder="ejemplo@pokemon.com">

            <label>Mensaje:</label>
            <textarea placeholder="Hola, estoy interesado en el Charizard..."></textarea>

            <button type="submit">Enviar Mensaje</button>
        </form>

    </div>

    <!-- FOOTER -->
    <footer>
        <p><strong>PokeTienda TCG</strong> - La mejor tienda para maestros Pokemon.</p>
        <p>C/ Pueblo Paleta, 123 - Kanto</p>
        <p>&copy; 2026 Todos los derechos reservados.</p>
    </footer>

</body>

</html>