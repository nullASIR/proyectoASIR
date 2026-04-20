<?php
session_start();
include 'database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cartas Sueltas - PokePimas Premium</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css?v=12">
</head>

<body>

    <!-- NAV BAR PREMIUM -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="inicio.php" class="logo">
                <span class="logo-icon">⚡</span> POKEPIMAS
            </a>

            <div class="nav-links">
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php" class="active">Cartas Sueltas</a>
                <a href="productos.php">Productos Sellados</a>
                <a href="contacto.php">Contacto</a>
            </div>

            <div class="user-actions">
                <a href="ver_carrito.php" class="nav-icon cart-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="user-profile">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?></span>
                        <div class="user-dropdown">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php
else: ?>
                    <a href="index.php" class="btn btn-outline">Iniciar Sesión</a>
                    <a href="registro.php" class="btn btn-primary">Registrarse</a>
                <?php
endif; ?>
            </div>
        </div>
    </nav>

    <div class="container main-content">

        <div class="section-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
            <h2>Cartas Sueltas</h2>
            
            <style>
                .pokelens-wrapper {
                    background: rgba(30, 41, 59, 0.6);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    border-top: 1px solid rgba(59, 130, 246, 0.5);
                    border-radius: 20px;
                    padding: 16px 24px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    flex-wrap: wrap;
                    transition: all 0.3s ease;
                }
                .pokelens-wrapper:hover {
                    background: rgba(30, 41, 59, 0.8);
                    border-color: rgba(59, 130, 246, 0.4);
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3), 0 0 15px rgba(59, 130, 246, 0.2);
                    transform: translateY(-2px);
                }
                .pokelens-info { display: flex; flex-direction: column; gap: 2px; }
                .pokelens-title {
                    font-weight: 800; font-size: 1.15rem; color: #fff;
                    display: flex; align-items: center; gap: 8px; margin: 0;
                    font-family: 'Montserrat', sans-serif;
                }
                .pokelens-title span.highlight {
                    background: linear-gradient(135deg, #60a5fa, #a78bfa);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                .pokelens-desc { font-size: 0.85rem; color: #94a3b8; margin: 0; }
                .pokelens-btn {
                    background: linear-gradient(135deg, #3b82f6, #6366f1);
                    color: white; padding: 10px 20px; border-radius: 50px;
                    font-weight: 600; cursor: pointer; display: flex; align-items: center;
                    gap: 8px; transition: all 0.3s ease; border: none; font-size: 0.95rem;
                    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); margin: 0;
                }
                .pokelens-btn:hover {
                    transform: scale(1.05); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
                }
                .loader-pokelens {
                    display: none; align-items: center; gap: 10px; color: #10b981;
                    font-weight: 600; font-size: 0.9rem; animation: pulseLens 1.5s infinite;
                }
                .spinner-lens {
                    width: 18px; height: 18px; border: 3px solid rgba(16, 185, 129, 0.2);
                    border-radius: 50%; border-top-color: #10b981; animation: spinLens 1s linear infinite;
                }
                @keyframes spinLens { to { transform: rotate(360deg); } }
                @keyframes pulseLens { 50% { opacity: 0.5; } }
            </style>

            <div class="pokelens-wrapper">
                <div class="pokelens-info">
                    <p class="pokelens-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        <span class="highlight">PokeLens AI</span>
                    </p>
                    <p class="pokelens-desc">Busca tu carta con una foto</p>
                </div>
                <input type="file" id="pokelens-input" accept="image/*" style="display: none;">
                <label for="pokelens-input" class="pokelens-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    Escanear
                </label>
                <div id="pokelens-loading" class="loader-pokelens">
                    <div class="spinner-lens"></div> Analizando...
                </div>
            </div>
        </div>

        <div id="pokelens-result" style="display: none;">
            <!-- Resultados inyectados por JS -->
        </div>

        <style>
            .cartas-lista {
                width: 100%;
                margin-bottom: 50px;
                font-family: Arial, sans-serif;
            }
            .carta-row {
                background: linear-gradient(145deg, rgba(44, 62, 80, 0.8), rgba(30, 41, 59, 0.9));
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                border: 1px solid rgba(148, 163, 184, 0.15);
                border-radius: 16px;
                padding: 16px 22px;
                margin-bottom: 20px;
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                color: white;
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease, border-color 0.3s ease;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                position: relative;
                overflow: hidden;
            }
            .carta-row::before {
                content: '';
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
                transform: translateX(-100%);
                transition: transform 0.6s ease;
                pointer-events: none;
            }
            .carta-row:hover {
                background: linear-gradient(145deg, rgba(51, 65, 85, 0.9), rgba(30, 41, 59, 0.95));
                transform: translateY(-4px) scale(1.02);
                box-shadow: 0 12px 25px rgba(0,0,0,0.5), 0 0 15px rgba(59, 130, 246, 0.3);
                border-color: rgba(96, 165, 250, 0.5);
            }
            .carta-row:hover::before {
                transform: translateX(100%);
            }
            
            /* Info Principal y Hover Imagen */
            .carta-info {
                display: flex;
                flex-direction: row;
                align-items: center;
                flex: 2;
            }
            .carta-icon-wrapper {
                position: relative;
                margin-right: 20px;
            }
            
            /* THUMBNAILS COMO CARTAS REALES TCG */
            .carta-img-trigger {
                width: 60px;
                height: 84px; /* Proporción real de cartas coleccionables */
                background-color: #0f172a;
                border: 2px solid #475569;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                box-shadow: 0 4px 8px rgba(0,0,0,0.6);
            }
            /* Brillo Foil Diagonal Interactivo para Thumbnails */
            .carta-img-trigger::after {
                content: '';
                position: absolute;
                top: -50%; left: -50%; width: 200%; height: 200%;
                background: linear-gradient(115deg, transparent 40%, rgba(255, 255, 255, 0.5) 45%, rgba(255, 255, 255, 0.8) 50%, rgba(255, 255, 255, 0.5) 55%, transparent 60%);
                transform: rotate(25deg) translateY(-100%);
                transition: transform 0.5s ease;
                pointer-events: none;
                mix-blend-mode: overlay;
                z-index: 10;
            }
            .carta-img-trigger:hover::after {
                transform: rotate(25deg) translateY(100%);
            }
            .carta-img-trigger:hover {
                transform: scale(1.15) rotate(3deg);
                box-shadow: 0 8px 16px rgba(0,0,0,0.7), 0 0 10px rgba(167, 139, 250, 0.5);
                border-color: #a78bfa;
            }
            .carta-img-trigger img.thumb {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 4px;
            }

            /* POPOVER GRAN TAMAÑO - EFECTO PREMIUM FOIL ESTRUCTURAL */
            .carta-hover-popover {
                position: absolute;
                top: -120px;
                left: 90px;
                width: 250px; 
                background: transparent;
                border: none;
                padding: 0;
                box-shadow: none;
                display: none;
                z-index: 200;
                perspective: 1000px;
            }
            .carta-icon-wrapper:hover .carta-hover-popover {
                display: block;
                animation: popInCard 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            }
            @keyframes popInCard {
                0% { opacity: 0; transform: scale(0.8) translateY(20px) rotateY(-15deg); }
                100% { opacity: 1; transform: scale(1) translateY(0) rotateY(0deg); }
            }
            
            .carta-popover-inner {
                position: relative;
                width: 100%;
                border-radius: 12px;
                box-shadow: 0 25px 40px rgba(0,0,0,0.8), 0 0 20px rgba(59, 130, 246, 0.4);
                overflow: hidden;
                transform-style: preserve-3d;
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            
            /* Brillo holográfico TCG base al popover */
            .carta-popover-inner::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(115deg, transparent 0%, rgba(255, 215, 0, 0.1) 30%, rgba(255, 255, 255, 0.4) 45%, rgba(255, 255, 255, 0.7) 50%, rgba(255, 255, 255, 0.4) 55%, rgba(255, 215, 0, 0.1) 70%, transparent 100%);
                background-size: 300% 300%;
                animation: premiumHolo 5s infinite linear;
                pointer-events: none;
                mix-blend-mode: color-dodge;
                opacity: 0.8;
                z-index: 5;
            }
            
            @keyframes premiumHolo {
                0% { background-position: 100% 100%; }
                100% { background-position: 0% 0%; }
            }

            .carta-hover-popover img {
                width: 100%;
                height: auto;
                max-height: 350px; /* Asegura visualización correcta si no está cropeada en sistema */
                display: block;
                border-radius: 12px;
                position: relative;
                z-index: 1;
            }

            /* Títulos y Metadatos */
            .carta-title {
                display: flex;
                flex-direction: column;
            }
            .carta-title h4 {
                margin: 0;
                font-size: 24px;
                font-weight: 800;
                color: #ffffff;
                text-shadow: 0px 2px 4px rgba(0,0,0,0.5);
                font-family: 'Montserrat', sans-serif;
            }

            /* Tags de Especificaciones */
            .carta-stats {
                display: flex;
                flex-direction: row;
                align-items: center;
                flex: 1.5;
                gap: 15px;
            }
            .stat-badge {
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 14px;
                color: white;
                display: flex;
                align-items: center;
                gap: 6px;
                font-weight: bold;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }
            .estado-badge {
                background: linear-gradient(to right, #2c3e50, #34495e);
                border: 1px solid #7f8c8d;
                border-left: 5px solid #e67e22;
                color: #ecf0f1;
            }
            .stock-badge {
                background: linear-gradient(135deg, #27ae60, #2ecc71);
                border: 1px solid #2ecc71;
            }

            /* Precio y Acción */
            .carta-actions {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: flex-end;
                flex: 1;
            }
            .carta-actions .precio {
                font-size: 22px;
                font-weight: 900;
                color: #f1c40f;
                margin-right: 18px;
                text-shadow: 1px 2px 3px rgba(0,0,0,0.4);
            }
            
            .btn-buy {
                background: linear-gradient(135deg, #2980b9, #3498db);
                color: white;
                border: none;
                padding: 10px 18px;
                font-size: 15px;
                font-weight: bold;
                border-radius: 6px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 6px;
                transition: transform 0.1s, box-shadow 0.1s;
                box-shadow: 0 3px 6px rgba(0,0,0,0.25);
            }
            .btn-buy:hover {
                background: linear-gradient(135deg, #3498db, #2980b9);
                transform: translateY(-1px);
                box-shadow: 0 5px 10px rgba(0,0,0,0.35);
            }
            .btn-buy:active {
                transform: translateY(1px);
                box-shadow: 0 2px 3px rgba(0,0,0,0.2);
            }
            @media (max-width: 800px) {
                .carta-row { 
                    flex-direction: column; 
                    align-items: flex-start; 
                    gap: 15px; 
                }
                .carta-info {
                    width: 100%;
                }
                .carta-stats { 
                    width: 100%; 
                    flex-wrap: wrap; 
                    justify-content: flex-start;
                }
                .carta-actions { 
                    width: 100%; 
                    justify-content: space-between; 
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                    padding-top: 15px;
                }
                /* Disable massive hover popovers on touch devices to avoid breaking viewport */
                .carta-hover-popover {
                    display: none !important;
                }
                .carta-img-trigger {
                    cursor: default;
                }
                .carta-img-trigger:hover {
                    transform: none;
                }
            }
            
            @media (max-width: 500px) {
                .carta-stats {
                    gap: 10px;
                }
                .stat-badge {
                    flex: 1; /* Make badges fill width on small screens */
                    justify-content: center;
                    font-size: 13px;
                    padding: 8px 10px;
                }
                .carta-actions {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 12px;
                }
                .carta-actions .precio {
                    margin-right: 0;
                    text-align: center;
                    font-size: 24px;
                }
                .btn-buy {
                    justify-content: center;
                    padding: 12px;
                    font-size: 16px;
                }
            }
        </style>

        <div class="cartas-lista">
            <?php
$sql = "SELECT * FROM productos WHERE tipo = 'carta'";
$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imgUrl = !empty($row['imagen']) ? "../" . htmlspecialchars($row['imagen']) : "";
?>
                <div class="carta-row">
                    <!-- INFO Y HOVER DE FOTO -->
                    <div class="carta-info">
                        <div class="carta-icon-wrapper">
                            <div class="carta-img-trigger">
                                <?php if ($imgUrl): ?>
                                    <img src="<?php echo $imgUrl; ?>" class="thumb" alt="Miniatura">
                                <?php
        else: ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bdc3c7" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <?php
        endif; ?>
                            </div>
                            
                            <!-- El Visualizador Oculto Premium -->
                            <div class="carta-hover-popover">
                                <div class="carta-popover-inner">
                                    <?php if ($imgUrl): ?>
                                        <img src="<?php echo $imgUrl; ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>">
                                    <?php
        else: ?>
                                        <div style="padding: 20px; background: #0f172a; text-align: center; color: #94a3b8; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 350px; gap: 10px;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                            Sin imagen
                                        </div>
                                    <?php
        endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carta-title">
                            <h4><?php echo htmlspecialchars($row['nombre']); ?></h4>
                        </div>
                    </div>
                    
                    <!-- FICHA TECNICA -->
                    <div class="carta-stats">
                        <div class="stat-badge estado-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <span>Estado: <strong><?php echo htmlspecialchars($row['estado']); ?></strong></span>
                        </div>
                        <div class="stat-badge stock-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <span>Stock: <strong><?php echo htmlspecialchars($row['stock']); ?></strong></span>
                        </div>
                    </div>
                    
                    <!-- PRECIO Y COMPRA -->
                    <div class="carta-actions">
                        <div class="precio"><?php echo $row['precio']; ?> €</div>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <button class="btn-buy" onclick="addToCart(<?php echo $row['id_producto']; ?>, '<?php echo htmlspecialchars(addslashes($row['nombre'])); ?>', <?php echo $row['precio']; ?>, '<?php echo htmlspecialchars($imgUrl); ?>')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                Añadir
                            </button>
                        <?php
        else: ?>
                            <button class="btn-buy" onclick="window.location.href='index.php?msg=Debes iniciar sesión para comprar'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                Añadir
                            </button>
                        <?php
        endif; ?>
                    </div>
                </div>
            <?php
    }
}
else {
    echo "<p style='color: white; padding: 20px; background-color: #34495e; border-radius: 5px; text-align: center;'>No hay cartas disponibles.</p>";
}
?>
        </div>

    </div>

    <!-- FOOTER PREMIUM -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="inicio.php" class="logo"><span class="logo-icon">⚡</span> POKEPIMAS</a>
                <p>El paraíso para coleccionistas y jugadores del Trading Card Game. La mayor selección de cartas y productos sellados.</p>
            </div>
            <div class="footer-links">
                <h4>Navegación</h4>
                <a href="inicio.php">Inicio</a>
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos</a>
            </div>
            <div class="footer-links">
                <h4>Legal</h4>
                <a href="#">Términos y Condiciones</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Política de Devoluciones</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 PokePimas TCG. Todos los derechos reservados. Desarrollado con ❤️ para entrenadores.</p>
        </div>
    </footer>

    <script src="../js/carrito.js"></script>
    <script src="../js/chatbot.js?v=4"></script>
    <script src="../js/pokelens.js?v=1"></script>

</body>

</html>
