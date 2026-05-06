<?php
session_start();
include 'database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php?msg=Acceso denegado. Solo administradores.");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'edit') {
        $id = (int)$_POST['id_producto'];
        $precio = (float)$_POST['precio'];
        $stock = (int)$_POST['stock'];
        
        $sql = "UPDATE Productos SET Precio = $precio, Stock = $stock WHERE IdProducto = $id";
        if (mysqli_query($conexion, $sql)) {
            $mensaje = "Producto actualizado correctamente.";
        } else {
            $mensaje = "Error al actualizar.";
        }
    } elseif ($action == 'delete') {
        $id = (int)$_POST['id_producto'];
        $sql = "DELETE FROM Productos WHERE IdProducto = $id";
        if (mysqli_query($conexion, $sql)) {
            $mensaje = "Producto eliminado correctamente.";
        } else {
            $mensaje = "Error al eliminar. Asegúrate de que no está en carritos activos.";
        }
    }
}

$sql = "SELECT * FROM Productos ORDER BY IdProducto DESC";
$result = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - PokePimas</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; color: white; background: rgba(30,41,59,0.8); border-radius: 8px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: left; }
        .admin-table th { background: rgba(59,130,246,0.5); }
        .admin-table tr:hover { background: rgba(255,255,255,0.05); }
        .admin-input { padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 80px; background: #fff; color: #000; }
        .btn-edit { background: #f39c12; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
        .btn-del { background: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo">⚡ POKEPIMAS ADMIN</a>
            <div class="nav-links">
                <a href="inicio.php">Volver a Inicio</a>
                <a href="admin.php" class="active">Gestionar Productos</a>
            </div>
        </div>
    </nav>

    <div class="container main-content" style="max-width: 1000px; margin: 0 auto; padding-top: 40px;">
        <h2>Gestión de Productos (Administrador)</h2>
        <?php if ($mensaje): ?>
            <div style="background: #2ecc71; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio (€)</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="id_producto" value="<?php echo $row['IdProducto']; ?>">
                    <td><?php echo $row['IdProducto']; ?></td>
                    <td><?php echo htmlspecialchars($row['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['Tipo']); ?></td>
                    <td><input type="number" step="0.01" name="precio" value="<?php echo $row['Precio']; ?>" class="admin-input"></td>
                    <td><input type="number" name="stock" value="<?php echo $row['Stock']; ?>" class="admin-input"></td>
                    <td>
                        <button type="submit" name="action" value="edit" class="btn-edit">Guardar</button>
                        <button type="submit" name="action" value="delete" class="btn-del" onclick="return confirm('¿Seguro que quieres borrar este producto?');">Borrar</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
