<?php
// 1. Rename strings in files
$files = [
    '../style/style.css?v=12',
    'ver_carrito.php',
    'verificar.php',
    'registro.php',
    'productos.php',
    'inicio.php',
    'index.php',
    'contacto.php',
    'cartas.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $c = str_replace('PokeTienda', 'PokeNexus', $c);
        $c = str_replace('POKETIENDA', 'POKENEXUS', $c);
        $c = str_replace('poketienda', 'pokenexus', $c);
        file_put_contents($f, $c);
    }
}

// 2. Insert dummy data into DB
include 'database.php';

// Clear current products for a clean run
mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conexion, "ALTER TABLE productos ADD COLUMN imagen VARCHAR(255)");
mysqli_query($conexion, "TRUNCATE TABLE productos");

// Insert 16 products
$productos = [
    [1, 10001, 'Charizard Base Set Holo', 350.00, 2, 'Casi Nuevo', 21.00, 'carta', 'img/card.png'],
    [2, 10002, 'Pikachu Illustrator', 15000.00, 1, 'Graduado PSA 9', 21.00, 'carta', 'img/card.png'],
    [3, 10003, 'Mewtwo VSTAR Universe', 25.50, 15, 'Mint', 21.00, 'carta', 'img/card.png'],
    [4, 10004, 'Lugia Neo Genesis 1st Ed', 450.00, 3, 'Excelente', 21.00, 'carta', 'img/card.png'],
    [5, 10005, 'Umbreon VMAX Alt Art', 400.00, 5, 'Mint', 21.00, 'carta', 'img/card.png'],
    [6, 10006, 'Gengar Fossil 1st Ed', 120.00, 4, 'Bueno', 21.00, 'carta', 'img/card.png'],
    [7, 10007, 'Rayquaza Gold Star', 800.00, 1, 'Graduado BGS 8.5', 21.00, 'carta', 'img/card.png'],
    [8, 10008, 'Machamp 1st Edition Shadowless', 90.00, 3, 'Jugada', 21.00, 'carta', 'img/card.png'],
    [9, 10009, 'Blastoise Base Set Holo', 180.00, 2, 'Muy Bueno', 21.00, 'carta', 'img/card.png'],

    [10, 20001, 'Caja Sobres Evoluciones', 120.00, 20, 'Nuevo Sellado', 21.00, 'producto', 'img/box.png'],
    [11, 20002, 'Elite Trainer Box 151', 65.00, 30, 'Nuevo Sellado', 21.00, 'producto', 'img/box.png'],
    [12, 20003, 'Caja Sobres Cielos Roaring', 150.00, 10, 'Nuevo Sellado', 21.00, 'producto', 'img/box.png'],
    [13, 20004, 'Pokeball Tin 2024', 22.50, 50, 'Nuevo Sellado', 21.00, 'producto', 'img/box.png'],
    [14, 20005, 'Collecion Premium Charizard ex', 45.00, 15, 'Nuevo Sellado', 21.00, 'producto', 'img/box.png'],

    [15, 30001, 'Álbum Premium 360 Cartas', 24.99, 45, 'Nuevo', 21.00, 'accesorio', 'img/binder.png'],
    [16, 30002, 'Fundas Protectoras Mate (100uds)', 7.50, 100, 'Nuevo', 21.00, 'accesorio', 'img/binder.png'],
    [17, 30003, 'Caja para Mazo Magnética', 15.00, 25, 'Nuevo', 21.00, 'accesorio', 'img/binder.png'],
    [18, 30004, 'Tapete de Juego Charizard', 30.00, 15, 'Nuevo', 21.00, 'accesorio', 'img/binder.png']
];

foreach ($productos as $p) {
    $sql = "INSERT INTO productos (id_producto, ean, nombre, precio, stock, estado, impuesto, tipo, imagen) VALUES (
        {$p[0]}, {$p[1]}, '{$p[2]}', {$p[3]}, {$p[4]}, '{$p[5]}', {$p[6]}, '{$p[7]}', '{$p[8]}'
    )";
    mysqli_query($conexion, $sql);
}

mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");

echo "OK - PokeNexus configured with products.";
?>

