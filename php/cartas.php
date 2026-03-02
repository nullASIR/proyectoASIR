<?php
session_start();
include 'database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cartas Sueltas - PokeNexus Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <!-- NAV BAR PREMIUM -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo">
                <span class="logo-icon">⚡</span> POKENEXUS
            </a>

            <div class="nav-links">
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php" class="active">Cartas Sueltas</a>
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

    <div class="main-content">

        <h2 style="color: white; border-bottom: 1px solid #333; padding-bottom: 10px;">Cartas Sueltas</h2>

        <div class="catalogo">
            <?php
$sql = "SELECT * FROM productos WHERE tipo = 'carta'";
$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imgUrl = !empty($row['imagen']) ? "../" . htmlspecialchars($row['imagen']) : "";
?>
                    <div class="carta">
                        <div class="foto-placeholder" style="background: white;">
                            <?php if ($imgUrl): ?>
                                <img src="<?php echo $imgUrl; ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: contain;">
                            <?php
        else: ?>
                                FOTO
                            <?php
        endif; ?>
                        </div>
                        <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
                        
                        <div class="ficha-tec">
                            <strong>Estado:</strong> <?php echo htmlspecialchars($row['estado']); ?><br>
                            <strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?> unid.<br>
                            <small style="color:var(--text-secondary);">EAN: <?php echo htmlspecialchars($row['ean']); ?></small>
                        </div>
                        
                        <div class="precio-row">
                            <div class="precio"><?php echo $row['precio']; ?> €</div>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <button onclick="addToCart(<?php echo $row['id_producto']; ?>, '<?php echo htmlspecialchars(addslashes($row['nombre'])); ?>', <?php echo $row['precio']; ?>)">Añadir</button>
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
    echo "<p style='color:white'>No hay cartas disponibles.</p>";
}
?>
        </div>

    </div>

    <!-- FOOTER PREMIUM -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="inicio.php" class="logo"><span class="logo-icon">⚡</span> POKENEXUS</a>
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
            <p>&copy; 2026 PokeNexus TCG. Todos los derechos reservados. Desarrollado con ❤️ para entrenadores.</p>
        </div>
    </footer>

    <script src="../js/carrito.js"></script>

</body>

</html>