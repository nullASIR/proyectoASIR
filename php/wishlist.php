<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php?msg=Debes iniciar sesión para ver tu wishlist.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Wishlist - PokePimas Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
    <style>
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .wishlist-item {
            background: linear-gradient(145deg, rgba(44, 62, 80, 0.8), rgba(30, 41, 59, 0.9));
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            color: white;
            border: 1px solid rgba(148, 163, 184, 0.15);
            transition: transform 0.3s;
        }
        .wishlist-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }
        .wishlist-item img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .wishlist-item h4 {
            margin: 10px 0;
            font-size: 16px;
        }
        .btn-remove {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>
<body onload="cargarWishlist()">
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo"><span class="logo-icon">⚡</span> POKEPIMAS</a>
            <div class="nav-links">
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos Sellados</a>
            </div>
            <div class="user-actions">
                <a href="ver_carrito.php" class="nav-icon cart-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </a>
            </div>
        </div>
    </nav>

    <div class="container main-content" style="max-width: 1000px; margin: 0 auto;">
        <h2>Mi Lista de Deseos</h2>
        <div id="wishlist-container" class="wishlist-grid">
            <!-- Cargado por JS -->
            <p>Cargando...</p>
        </div>
    </div>

    <script src="../js/carrito.js?v=2"></script>
    <script>
        async function cargarWishlist() {
            let contenedor = document.getElementById('wishlist-container');
            let formData = new FormData();
            formData.append('action', 'get');

            try {
                let res = await fetch('api_wishlist.php', { method: 'POST', body: formData });
                let data = await res.json();
                
                contenedor.innerHTML = '';
                
                if (data.success && data.items.length > 0) {
                    data.items.forEach(item => {
                        let img = item.Imagen ? `<img src="${item.Imagen}" alt="${item.Nombre}">` : `<div style="height:200px;background:#334155;display:flex;align-items:center;justify-content:center;border-radius:8px;margin-bottom:10px;">📦</div>`;
                        contenedor.innerHTML += `
                            <div class="wishlist-item" id="wl-item-${item.IdProducto}">
                                ${img}
                                <h4>${item.Nombre}</h4>
                                <p>${item.Precio} €</p>
                                <button onclick="addToCart(${item.IdProducto}, '${item.Nombre.replace(/'/g, "\\'")}', ${item.Precio}, '${item.Imagen ? item.Imagen : ''}')" style="background:#3498db;color:white;border:none;padding:8px;border-radius:4px;cursor:pointer;width:100%;margin-bottom:5px;">Añadir al Carrito</button>
                                <button onclick="removerWishlist(${item.IdProducto})" class="btn-remove">Eliminar</button>
                            </div>
                        `;
                    });
                } else {
                    contenedor.innerHTML = '<p>Tu Wishlist está vacía.</p>';
                }
            } catch(e) {
                contenedor.innerHTML = '<p>Error cargando la wishlist.</p>';
            }
        }

        async function removerWishlist(id) {
            await toggleWishlist(id);
            document.getElementById(`wl-item-${id}`).remove();
        }
    </script>
</body>
</html>
