<?php
session_start();
include 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit();
}

$userId = $_SESSION['usuario_id'];

// Get or Create Cart
$qCart = $conexion->query("SELECT Id FROM Cart WHERE UserId = $userId");
if ($qCart->rowCount() == 0) {
    $conexion->query("INSERT INTO Cart (UserId) VALUES ($userId)");
    $cartId = $conexion->lastInsertId();
} else {
    $row = $qCart->fetch(PDO::FETCH_ASSOC);
    $cartId = $row['Id'];
}

// Ensure proper method reading
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'add') {
    $idProd = (int)($_POST['id'] ?? 0);
    // start trans
    $conexion->beginTransaction();
    $qStock = $conexion->query("SELECT Stock FROM Productos WHERE IdProducto = $idProd FOR UPDATE");
    $rowStock = $qStock->fetch(PDO::FETCH_ASSOC);
    
    if ($rowStock && $rowStock['Stock'] > 0) {
        $conexion->query("UPDATE Productos SET Stock = Stock - 1 WHERE IdProducto = $idProd");
        
        // check if in cart
        $qItem = $conexion->query("SELECT Id FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd");
        if ($qItem->rowCount() > 0) {
            $conexion->query("UPDATE CartItem SET Cantidad = Cantidad + 1 WHERE CartId = $cartId AND ProductoId = $idProd");
        } else {
            $conexion->query("INSERT INTO CartItem (CartId, ProductoId, Cantidad) VALUES ($cartId, $idProd, 1)");
        }
        $conexion->commit();
        echo json_encode(['success' => true]);
    } else {
        $conexion->rollBack();
        echo json_encode(['success' => false, 'message' => 'Sin stock']);
    }
} elseif ($action == 'remove') {
    // remove completely or decrement? Let's say remove completely from cart and restore stock
    $idProd = (int)($_POST['id'] ?? 0);
    $conexion->beginTransaction();
    $qItem = $conexion->query("SELECT Cantidad FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd FOR UPDATE");
    if ($rowItem = $qItem->fetch(PDO::FETCH_ASSOC)) {
        $cantidad = $rowItem['Cantidad'];
        $conexion->query("UPDATE Productos SET Stock = Stock + $cantidad WHERE IdProducto = $idProd");
        $conexion->query("DELETE FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd");
        $conexion->commit();
        echo json_encode(['success' => true]);
    } else {
        $conexion->rollBack();
        echo json_encode(['success' => false, 'message' => 'Not in cart']);
    }
} elseif ($action == 'clear') {
    $conexion->beginTransaction();
    $qItems = $conexion->query("SELECT ProductoId, Cantidad FROM CartItem WHERE CartId = $cartId FOR UPDATE");
    while ($rowItem = $qItems->fetch(PDO::FETCH_ASSOC)) {
        $idProd = $rowItem['ProductoId'];
        $cantidad = $rowItem['Cantidad'];
        $conexion->query("UPDATE Productos SET Stock = Stock + $cantidad WHERE IdProducto = $idProd");
    }
    $conexion->query("DELETE FROM CartItem WHERE CartId = $cartId");
    $conexion->commit();
    echo json_encode(['success' => true]);
} elseif ($action == 'get') {
    $q = $conexion->query("SELECT c.Cantidad, p.IdProducto as id, p.Nombre as nombre, p.Precio as precio, p.Imagen as imagen FROM CartItem c JOIN Productos p ON c.ProductoId = p.IdProducto WHERE c.CartId = $cartId");
    $items = [];
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $r;
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
