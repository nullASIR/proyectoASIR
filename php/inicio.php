
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PokeTienda - Inicio</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="logo">
            POKETIENDA
        </div>

        <div class="user-options">
            <a href="inicio.php" style="color: #cc0000;">Inicio</a>
            <a href="cartas.php">Cartas</a>
            <a href="productos.php">Productos</a>
            <a href="contacto.php">Contacto</a>
            <span>Hola,
                <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </span>
            <a href="logout.php">Salir</a>
        </div>
    </header>

    <!-- CUERPO PRINCIPAL (LANDING) -->
    <div class="main-content" style="text-align: center;">

        <h1 style="color: white; font-size: 3em; margin-bottom: 10px;">¡Bienvenido Entrenador!</h1>
        <p style="color: #bbb; font-size: 1.2em; margin-bottom: 50px;">Elige qué categoría quieres explorar hoy.</p>

        <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">

            <!-- TARJETA ENLACE CARTAS -->
            <a href="cartas.php" style="text-decoration: none;">
                <div class="carta" style="width: 300px; padding: 40px; border-color: #cc0000;">
                    <h3 style="color: #cc0000; font-size: 2em; margin: 0;">CARTAS SUELTAS</h3>
                    <p style="color: #ccc;">Consigue esa carta que le falta a tu colección.</p>
                </div>
            </a>

            <!-- TARJETA ENLACE PRODUCTOS -->
            <a href="productos.php" style="text-decoration: none;">
                <div class="carta" style="width: 300px; padding: 40px; border-color: #007bff;">
                    <h3 style="color: #007bff; font-size: 2em; margin: 0;">PRODUCTOS</h3>
                    <p style="color: #ccc;">Cajas, sobres, fundas y mucho más.</p>
                </div>
            </a>

        </div>

    </div>

    <!-- FOOTER -->
    <footer>
        <p><strong>PokeTienda TCG</strong> - La mejor tienda para maestros Pokemon.</p>
        <p>C/ Pueblo Paleta, 123 - Kanto</p>
        <p>&copy; 2026 Todos los derechos reservados.</p>
    </footer>

</body>

</html>