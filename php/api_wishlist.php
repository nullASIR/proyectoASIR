<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Inicia sesión para usar la Wishlist']);
    exit();
}

$userId = $_SESSION['usuario_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$idProd = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($action == 'toggle' && $idProd > 0) {
    $q = mysqli_query($conexion, "SELECT Id FROM Wishlist WHERE UserId = $userId AND ProductoId = $idProd");
    if (mysqli_num_rows($q) > 0) {
        mysqli_query($conexion, "DELETE FROM Wishlist WHERE UserId = $userId AND ProductoId = $idProd");
        echo json_encode(['success' => true, 'status' => 'removed']);
    } else {
        mysqli_query($conexion, "INSERT INTO Wishlist (UserId, ProductoId) VALUES ($userId, $idProd)");
        echo json_encode(['success' => true, 'status' => 'added']);
    }
} elseif ($action == 'get') {
    $q = mysqli_query($conexion, "SELECT p.* FROM Wishlist w JOIN Productos p ON w.ProductoId = p.IdProducto WHERE w.UserId = $userId");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $items[] = $r;
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
