<?php
include 'database.php';

$queries = [
    "ALTER TABLE user ADD COLUMN IsAdmin BOOLEAN DEFAULT FALSE",
    "UPDATE user SET IsAdmin = TRUE WHERE Mail = 'admin@pimas.com'",
    "CREATE TABLE IF NOT EXISTS Cart (
        Id INT PRIMARY KEY AUTO_INCREMENT,
        UserId INT,
        FOREIGN KEY (UserId) REFERENCES user(Id)
    ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS CartItem (
        Id INT PRIMARY KEY AUTO_INCREMENT,
        CartId INT,
        ProductoId INT,
        Cantidad INT,
        FOREIGN KEY (CartId) REFERENCES Cart(Id),
        FOREIGN KEY (ProductoId) REFERENCES Productos(IdProducto)
    ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS Wishlist (
        Id INT PRIMARY KEY AUTO_INCREMENT,
        UserId INT,
        ProductoId INT,
        FOREIGN KEY (UserId) REFERENCES user(Id),
        FOREIGN KEY (ProductoId) REFERENCES Productos(IdProducto)
    ) ENGINE=InnoDB"
];

foreach ($queries as $q) {
    if ($conexion->query($q)) {
        echo "OK: $q\n";
    } else {
        echo "ERROR: " . $conexion->error . " - $q\n";
    }
}
?>
