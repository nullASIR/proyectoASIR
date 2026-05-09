<?php
session_start();
include 'database.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php?msg=Acceso denegado. Solo administradores.");
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
        if ($conexion->query($sql)) {
            $mensaje = "Producto actualizado correctamente.";
        } else {
            $mensaje = "Error al actualizar.";
        }
    } elseif ($action == 'delete') {
        $id = (int)$_POST['id_producto'];
        $sql = "DELETE FROM Productos WHERE IdProducto = $id";
        if ($conexion->query($sql)) {
            $mensaje = "Producto eliminado correctamente.";
        } else {
            $mensaje = "Error al eliminar. Asegúrate de que no está en carritos activos.";
        }
    }
}

$sql = "SELECT * FROM Productos ORDER BY IdProducto DESC";
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - PokePimas</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #38bdf8;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #e0f2fe, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            color: #10b981;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admin-grid {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: blur(12px);
        }

        .admin-grid-header {
            display: grid;
            grid-template-columns: 80px 2fr 1fr 140px 140px 220px;
            gap: 16px;
            padding: 20px 24px;
            background: rgba(15, 23, 42, 0.6);
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            align-items: center;
        }

        .admin-grid-row {
            display: grid;
            grid-template-columns: 80px 2fr 1fr 140px 140px 220px;
            gap: 16px;
            padding: 16px 24px;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }

        .admin-grid-row:last-child {
            border-bottom: none;
        }

        .admin-grid-row:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .cell-id {
            color: #64748b;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .cell-name {
            font-weight: 600;
            color: #f1f5f9;
            font-size: 1.05rem;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(255,255,255,0.1);
            display: inline-block;
        }
        
        .badge-fuego { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .badge-agua { background: rgba(56, 189, 248, 0.2); color: #7dd3fc; }
        .badge-planta { background: rgba(34, 197, 94, 0.2); color: #86efac; }
        .badge-eléctrico { background: rgba(234, 179, 8, 0.2); color: #fde047; }
        .badge-psíquico { background: rgba(217, 70, 239, 0.2); color: #f0abfc; }

        .input-group {
            display: flex;
            align-items: center;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .input-group:focus-within {
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }

        .input-group span {
            padding: 0 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .admin-input {
            width: 100%;
            background: transparent;
            border: none;
            color: #f1f5f9;
            padding: 10px 12px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            outline: none;
            -moz-appearance: textfield;
            font-size: 1rem;
        }

        .admin-input::-webkit-outer-spin-button,
        .admin-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .cell-actions {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-save {
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.2);
            flex: 1;
        }

        .btn-save:hover {
            background: #38bdf8;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .btn-del {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 8px 12px;
        }

        .btn-del:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.5;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" style="stroke: #fbbf24;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                POKEPIMAS ADMIN
            </a>
            <div class="nav-links">
                <a href="../index.php">Volver a Inicio</a>
                <a href="admin.php" class="active">Gestionar Productos</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <h2 class="header-title">Inventario y Precios</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="admin-grid">
            <div class="admin-grid-header">
                <div>ID</div>
                <div>Nombre</div>
                <div>Tipo</div>
                <div>Precio</div>
                <div>Stock</div>
                <div>Acciones</div>
            </div>
            
            <?php while ($row = $result->fetch_assoc()): ?>
            <form method="POST" action="admin.php" class="admin-grid-row">
                <input type="hidden" name="id_producto" value="<?php echo $row['IdProducto']; ?>">
                
                <div class="cell-id">#<?php echo str_pad($row['IdProducto'], 3, '0', STR_PAD_LEFT); ?></div>
                
                <div class="cell-name"><?php echo htmlspecialchars($row['Nombre']); ?></div>
                
                <div class="cell-type">
                    <span class="badge badge-<?php echo strtolower(htmlspecialchars($row['Tipo'])); ?>">
                        <?php echo htmlspecialchars($row['Tipo']); ?>
                    </span>
                </div>
                
                <div class="cell-price">
                    <div class="input-group">
                        <span>€</span>
                        <input type="number" step="0.01" name="precio" value="<?php echo $row['Precio']; ?>" class="admin-input">
                    </div>
                </div>
                
                <div class="cell-stock">
                    <div class="input-group" style="padding-left: 12px;">
                        <input type="number" name="stock" value="<?php echo $row['Stock']; ?>" class="admin-input" style="padding-left: 0;">
                    </div>
                </div>
                
                <div class="cell-actions">
                    <button type="submit" name="action" value="edit" class="btn-action btn-save">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Guardar
                    </button>
                    <button type="submit" name="action" value="delete" class="btn-action btn-del" onclick="return confirm('¿Estás seguro de que deseas eliminar permanentemente a <?php echo addslashes(htmlspecialchars($row['Nombre'])); ?>?');" title="Eliminar Producto">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
            </form>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
