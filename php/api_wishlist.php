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
    $q = $conexion->query("SELECT Id FROM Wishlist WHERE UserId = $userId AND ProductoId = $idProd");
    if ($q->num_rows > 0) {
        $conexion->query("DELETE FROM Wishlist WHERE UserId = $userId AND ProductoId = $idProd");
        echo json_encode(['success' => true, 'status' => 'removed']);
    } else {
        $conexion->query("INSERT INTO Wishlist (UserId, ProductoId) VALUES ($userId, $idProd)");
        echo json_encode(['success' => true, 'status' => 'added']);
    }
} elseif ($action == 'get') {
    $q = $conexion->query("SELECT p.* FROM Wishlist w JOIN Productos p ON w.ProductoId = p.IdProducto WHERE w.UserId = $userId");
    $items = [];
    while ($r = $q->fetch_assoc()) {
        $items[] = $r;
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
