<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito - PokeTienda</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body onload="mostrarCarrito()">

    <!-- HEADER -->
    <header>
        <div class="logo">
            POKETIENDA
        </div>

        <div class="user-options">
            <a href="inicio.php">Inicio</a>
            <a href="cartas.php">Cartas</a>
            <a href="productos.php">Productos</a>
            <a href="contacto.php">Contacto</a>
            <a href="ver_carrito.php" style="color: #cc0000;">Carrito</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php">Salir</a>
            <?php else: ?>
                <a href="index.php">Iniciar Sesión</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="main-content" style="max-width: 800px; margin: 0 auto;">
        <h2>Tu Carrito de Compras</h2>

        <div id="lista-carrito" style="color: white; margin-bottom: 20px;">
            <!-- Cargado via JS -->
        </div>

        <div style="text-align: right; color: white;">
            <h3>Total: <span id="total-carrito">0.00 €</span></h3>
            <button onclick="vaciarCarrito()"
                style="background: #cc0000; color: white; padding: 10px 20px; border: none; cursor: pointer;">Vaciar
                Carrito</button>
            <button
                style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">Finalizar
                Compra</button>
        </div>
    </div>

    <script src="../js/carrito.js"></script>

</body>

</html>