<?php
session_start();
include 'database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos Sellados - PokePimas Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
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
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php" class="active">Productos Sellados</a>
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
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <a href="admin.php" class="dropdown-item">Panel Admin</a>
                            <?php endif; ?>
                            <a href="wishlist.php" class="dropdown-item">Mi Wishlist</a>
                            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php
else: ?>
                    <a href="../index.php" class="btn btn-outline">Iniciar Sesión</a>
                    <a href="registro.php" class="btn btn-primary">Registrarse</a>
                <?php
endif; ?>
            </div>
        </div>
    </nav>

    <!-- CUERPO PRINCIPAL -->
    <div class="container main-content">

        <div class="section-head">
            <h2>Productos Sellados & Accesorios</h2>
        </div>

        <div class="search-filter" style="margin-bottom: 40px; display: flex; justify-content: center; width: 100%;">
            <form method="GET" action="productos.php" style="display: flex; gap: 8px; width: 100%; max-width: 450px; background: rgba(30, 41, 59, 0.7); padding: 6px; border-radius: 50px; border: 1px solid rgba(148, 163, 184, 0.2); box-shadow: 0 4px 20px rgba(0,0,0,0.15); align-items: center; box-sizing: border-box;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 15px; flex-shrink: 0;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="q" placeholder="Buscar productos..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" style="flex: 1; padding: 10px 10px; border-radius: 50px; border: none; background: transparent; color: #fff; outline: none; font-size: 15px; min-width: 0;">
                <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 10px 25px; margin: 0; flex-shrink: 0; white-space: nowrap;">Buscar</button>
            </form>
        </div>

        <div class="catalogo">
            <?php
// Mostramos todos los productos que no sean cartas sueltas (productos sellados, accesorios, etc)
$search = isset($_GET['q']) ? mysqli_real_escape_string($conexion, $_GET['q']) : '';
$sql = "SELECT * FROM Productos WHERE Tipo != 'Carta Suelta'";
if ($search) {
    $sql .= " AND Nombre LIKE '%$search%'";
}
$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imgUrl = isset($row['Imagen']) && !empty($row['Imagen']) ? htmlspecialchars($row['Imagen']) : "";
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
                        <h4><?php echo htmlspecialchars($row['Nombre']); ?></h4>
                        
                        <div class="ficha-tec">
                            <strong>Estado:</strong> <?php echo htmlspecialchars($row['Estado']); ?>
                        </div>
                        
                        <div class="precio-row">
                            <div class="precio"><?php echo $row['Precio']; ?> €</div>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <div style="display:flex; gap:10px;">
                                    <button id="wishlist-btn-<?php echo $row['IdProducto']; ?>" style="background: #e74c3c; padding: 8px 12px; border:none; border-radius:4px; cursor:pointer;" onclick="toggleWishlist(<?php echo $row['IdProducto']; ?>)">🤍</button>
                                    <button onclick="addToCart(<?php echo $row['IdProducto']; ?>, '<?php echo htmlspecialchars(addslashes($row['Nombre'])); ?>', <?php echo $row['Precio']; ?>, '<?php echo htmlspecialchars($imgUrl); ?>')">Añadir</button>
                                </div>
                            <?php
        else: ?>
                                <button onclick="window.location.href='../index.php?msg=Debes iniciar sesión para comprar'">Añadir</button>
                            <?php
        endif; ?>
                        </div>
                    </div>
                    <?php
    }
}
else {
    echo "<p>No hay productos disponibles.</p>";
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

    <script src="../js/carrito.js?v=2"></script>
    <script src="../js/chatbot.js?v=4"></script>

</body>

</html>
