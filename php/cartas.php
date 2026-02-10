<?php
session_start();
include 'database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cartas Sueltas - PokeTienda</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <header>
        <div class="logo">
            POKETIENDA
        </div>
        
        <div class="user-options">
            <a href="inicio.php">Inicio</a>
            <a href="cartas.php" style="color: #cc0000;">Cartas</a>
            <a href="productos.php">Productos</a>
            <a href="contacto.php">Contacto</a>
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

    <div class="main-content">

        <h2 style="color: white; border-bottom: 1px solid #333; padding-bottom: 10px;">Cartas Sueltas</h2>

        <div class="catalogo">
            <?php
            $sql = "SELECT * FROM productos";
            $result = mysqli_query($conexion, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $img = !empty($row['imagen']) ? $row['imagen'] : "FOTO CARTA";
                    ?>
                    <div class="carta">
                        <div class="foto-placeholder">
                            <?php echo $img; ?>
                        </div>
                        <h4>
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </h4>
                        <p style="color:#aaa; font-size:0.9em;">
                            <?php echo htmlspecialchars($row['descripcion']); ?>
                        </p>
                        <div class="precio">
                            <?php echo $row['precio']; ?> €
                        </div>
                        <button
                            onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nombre']); ?>', <?php echo $row['precio']; ?>)">Añadir</button>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='color:white'>No hay cartas disponibles.</p>";
            }
            ?>
        </div>

    </div>

    <footer>
        <p><strong>PokeTienda TCG</strong> - La mejor tienda para maestros Pokemon.</p>
        <p>C/ Pueblo Paleta, 123 - Kanto</p>
        <p>&copy; 2026 Todos los derechos reservados.</p>
    </footer>

    <script src="../js/carrito.js"></script>

</body>

</html>