<?php
session_start();
include 'database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PokePimas - Inicio</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>

    <!-- NAV BAR PREMIUM -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo">
                <span class="logo-icon">⚡</span> POKEPIMAS
            </a>

            <div class="nav-links">
                <a href="inicio.php" class="active">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos Sellados</a>
                <a href="contacto.php">Contacto</a>
            </div>

            <div class="user-actions">
                <a href="ver_carrito.php" class="nav-icon cart-icon">
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

    <!-- CUERPO PRINCIPAL -->
    <div class="container main-content">

        <!-- HERO SECTION -->
        <section class="hero">
            <h1>El Nexo de los Entrenadores</h1>
            <p>Descubre cartas raras, completa tus colecciones y encuentra los productos sellados más exclusivos del mundo Pokémon TCG.</p>
            <a href="cartas.php" class="btn btn-secondary">Explorar Catálogo</a>
        </section>

        <!-- VENTAJAS / FEATURES -->
        <section class="features-grid">
            <div class="feature-box">
                <span class="f-icon">🚀</span>
                <h3>Envío Relámpago</h3>
                <p>Envíos protegidos y asegurados en 24/48 horas para que no dejes de jugar.</p>
            </div>
            <div class="feature-box">
                <span class="f-icon">💎</span>
                <h3>100% Originales</h3>
                <p>Todas nuestras cartas y productos son verificados por expertos.</p>
            </div>
            <div class="feature-box">
                <span class="f-icon">🛡️</span>
                <h3>Pago Seguro</h3>
                <p>Pasarelas cifradas y protección total al comprador en cada transacción.</p>
            </div>
        </section>

        <!-- CATEGORIAS -->
        <h2 class="section-head text-center" style="margin-bottom:30px;">Explora Nuestras Categorías</h2>
        <section class="home-categories">
            <a href="cartas.php" class="cat-card c-cartas">
                <h3>Cartas Sueltas</h3>
            </a>
            <a href="productos.php" class="cat-card c-productos">
                <h3>Productos Sellados</h3>
            </a>
        </section>

        <!-- DESTACADOS DINÁMICOS -->
        <div class="section-head">
            <h2>Novedades Destacadas</h2>
        </div>
        
        <div class="catalogo">
            <?php
// Sacar los 4 productos más caros o los últimos 4
$sql = "SELECT * FROM productos ORDER BY id_producto DESC LIMIT 4";
$result = mysqli_query($conexion, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imgUrl = !empty($row['imagen']) ? "../" . htmlspecialchars($row['imagen']) : "";
?>
                    <div class="carta">
                        <div class="foto-placeholder" style="background: white;">
                            <?php if ($imgUrl): ?>
                                <img src="<?php echo $imgUrl; ?>" alt="Foto">
                            <?php
        else: ?>
                                FOTO
                            <?php
        endif; ?>
                        </div>
                        <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
                        
                        <div class="ficha-tec">
                            <strong>Estado:</strong> <?php echo htmlspecialchars($row['estado']); ?><br>
                            <strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?> unid.
                        </div>
                        
                        <div class="precio-row">
                            <div class="precio"><?php echo $row['precio']; ?> €</div>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <button onclick="addToCart(<?php echo $row['id_producto']; ?>, '<?php echo htmlspecialchars(addslashes($row['nombre'])); ?>', <?php echo $row['precio']; ?>, '<?php echo htmlspecialchars($imgUrl); ?>')">Añadir</button>
                            <?php
        else: ?>
                                <button onclick="window.location.href='index.php?msg=Debes iniciar sesión para comprar'">Añadir</button>
                            <?php
        endif; ?>
                        </div>
                    </div>
            <?php
    }
}
else {
    echo "<p>No hay productos destacados por el momento.</p>";
}
?>
        </div>

    </div>

    <!-- FOOTER PREMIUM -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="inicio.php" class="logo"><span class="logo-icon">⚡</span> POKEPIMAS</a>
                <p>El paraíso para coleccionistas y jugadores del Trading Card Game. La mayor selección de cartas y productos sellados.</p>
            </div>
            <div class="footer-links">
                <h4>Navegación</h4>
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos</a>
            </div>
            <div class="footer-links">
                <h4>Legal</h4>
                <a href="#">Términos y Condiciones</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Política de Devoluciones</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 PokePimas TCG. Todos los derechos reservados. Desarrollado con ❤️ para entrenadores.</p>
        </div>
    </footer>

    <script src="../js/carrito.js"></script>
    <script src="../js/chatbot.js?v=4"></script>
</body>
</html>
