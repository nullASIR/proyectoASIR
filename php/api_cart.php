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
$qCart = mysqli_query($conexion, "SELECT Id FROM Cart WHERE UserId = $userId");
if (mysqli_num_rows($qCart) == 0) {
    mysqli_query($conexion, "INSERT INTO Cart (UserId) VALUES ($userId)");
    $cartId = mysqli_insert_id($conexion);
} else {
    $row = mysqli_fetch_assoc($qCart);
    $cartId = $row['Id'];
}

// Ensure proper method reading
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'add') {
    $idProd = (int)($_POST['id'] ?? 0);
    // start trans
    mysqli_begin_transaction($conexion);
    $qStock = mysqli_query($conexion, "SELECT Stock FROM Productos WHERE IdProducto = $idProd FOR UPDATE");
    $rowStock = mysqli_fetch_assoc($qStock);
    
    if ($rowStock && $rowStock['Stock'] > 0) {
        mysqli_query($conexion, "UPDATE Productos SET Stock = Stock - 1 WHERE IdProducto = $idProd");
        
        // check if in cart
        $qItem = mysqli_query($conexion, "SELECT Id FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd");
        if (mysqli_num_rows($qItem) > 0) {
            mysqli_query($conexion, "UPDATE CartItem SET Cantidad = Cantidad + 1 WHERE CartId = $cartId AND ProductoId = $idProd");
        } else {
            mysqli_query($conexion, "INSERT INTO CartItem (CartId, ProductoId, Cantidad) VALUES ($cartId, $idProd, 1)");
        }
        mysqli_commit($conexion);
        echo json_encode(['success' => true]);
    } else {
        mysqli_rollback($conexion);
        echo json_encode(['success' => false, 'message' => 'Sin stock']);
    }
} elseif ($action == 'remove') {
    // remove completely or decrement? Let's say remove completely from cart and restore stock
    $idProd = (int)($_POST['id'] ?? 0);
    mysqli_begin_transaction($conexion);
    $qItem = mysqli_query($conexion, "SELECT Cantidad FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd FOR UPDATE");
    if ($rowItem = mysqli_fetch_assoc($qItem)) {
        $cantidad = $rowItem['Cantidad'];
        mysqli_query($conexion, "UPDATE Productos SET Stock = Stock + $cantidad WHERE IdProducto = $idProd");
        mysqli_query($conexion, "DELETE FROM CartItem WHERE CartId = $cartId AND ProductoId = $idProd");
        mysqli_commit($conexion);
        echo json_encode(['success' => true]);
    } else {
        mysqli_rollback($conexion);
        echo json_encode(['success' => false, 'message' => 'Not in cart']);
    }
} elseif ($action == 'clear') {
    mysqli_begin_transaction($conexion);
    $qItems = mysqli_query($conexion, "SELECT ProductoId, Cantidad FROM CartItem WHERE CartId = $cartId FOR UPDATE");
    while ($rowItem = mysqli_fetch_assoc($qItems)) {
        $idProd = $rowItem['ProductoId'];
        $cantidad = $rowItem['Cantidad'];
        mysqli_query($conexion, "UPDATE Productos SET Stock = Stock + $cantidad WHERE IdProducto = $idProd");
    }
    mysqli_query($conexion, "DELETE FROM CartItem WHERE CartId = $cartId");
    mysqli_commit($conexion);
    echo json_encode(['success' => true]);
} elseif ($action == 'get') {
    $q = mysqli_query($conexion, "SELECT c.Cantidad, p.IdProducto as id, p.Nombre as nombre, p.Precio as precio, p.Imagen as imagen FROM CartItem c JOIN Productos p ON c.ProductoId = p.IdProducto WHERE c.CartId = $cartId");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $items[] = $r;
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
