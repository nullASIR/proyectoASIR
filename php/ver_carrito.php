<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?msg=Debes iniciar sesión para ver tu carrito.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito - PokeNexus Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body onload="mostrarCarrito()">

    <!-- NAV BAR PREMIUM -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo">
                <span class="logo-icon">⚡</span> POKENEXUS
            </a>

            <div class="nav-links">
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos Sellados</a>
                <a href="contacto.php">Contacto</a>
            </div>

            <div class="user-actions">
                <a href="ver_carrito.php" class="nav-icon cart-icon active">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="user-profile">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?></span>
                        <div class="user-dropdown">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php
else: ?>
                    <a href="index.php" class="btn btn-outline">Iniciar Sesión</a>
                    <a href="registro.php" class="btn btn-primary">Registrarse</a>
                <?php
endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-content" style="max-width: 800px; margin: 0 auto;">
        <h2>Tu Carrito de Compras</h2>

        <div id="lista-carrito" style="color: white; margin-bottom: 20px;">
            <!-- Cargado via JS -->
        </div>

        <div style="text-align: right; color: white;">
            <h3>Total: <span id="total-carrito">0.00 €</span></h3>
            <br>
            <button onclick="vaciarCarrito()" class="btn btn-outline" style="margin-right: 15px;">Vaciar Carrito</button>
            <button class="btn btn-primary">Finalizar Compra</button>
        </div>
    </div>

    <script src="../js/carrito.js"></script>

</body>

</html>