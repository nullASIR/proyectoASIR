<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - PokePimas Premium</title>
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
                <a href="cartas.php">Cartas Sueltas</a>
                <a href="productos.php">Productos Sellados</a>
                <a href="contacto.php" class="active">Contacto</a>
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

    <!-- HERO CONTACTO -->
    <section class="contact-hero">
        <div class="hero-bg-glow"></div>
        <div class="container text-center">
            <h1 class="gradient-text">Estamos aquí para ayudarte</h1>
            <p class="hero-subtitle">¿Buscas una carta en específico? ¿Tienes problemas con un pedido? Ponte en contacto con nuestro equipo de Expertos Pokémon.</p>
        </div>
    </section>

    <!-- CONTACTO MAIN SECTION -->
    <section class="contact-section container">
        <div class="contact-grid">
            
            <!-- INFO COLUMN -->
            <div class="contact-info-card">
                <div class="info-header">
                    <h3>Información de Contacto</h3>
                    <p>Contacta con nosotros mediante cualquiera de estas vías y te responderemos en 24h.</p>
                </div>
                
                <div class="info-items">
                    <div class="info-item">
                        <div class="icon-box">📍</div>
                        <div>
                            <h4>Nuestra Tienda</h4>
                            <p>IES Ribera del Tajo<br>Talavera de la Reina</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon-box">✉️</div>
                        <div>
                            <h4>Soporte Email</h4>
                            <p>soporte@pokepimas.tcg.com<br>info@pokepimas.tcg.com</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon-box">📞</div>
                        <div>
                            <h4>Llamadas</h4>
                            <p>+34 900 123 456<br><small>Lun-Vie, 9:00 - 18:00</small></p>
                        </div>
                    </div>
                </div>

                <div class="social-links">
                    <span>Síguenos:</span>
                    <a href="#" class="social-icon">Tw</a>
                    <a href="#" class="social-icon">Ig</a>
                    <a href="#" class="social-icon">Fb</a>
                </div>
                
                <!-- Decoración -->
                <div class="pokeball-deco"></div>
            </div>

            <!-- FORM COLUMN -->
            <div class="contact-form-wrapper">
                <h3>Envíanos un mensaje</h3>
                <form class="premium-form" action="#" method="POST" onsubmit="event.preventDefault(); alert('¡Mensaje enviado con éxito! Nuestro equipo te contactará pronto.');">
                    
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" id="nombre" name="nombre" required placeholder=" ">
                            <label for="nombre">Tu Nombre</label>
                            <span class="focus-border"></span>
                        </div>
                        
                        <div class="input-group">
                            <input type="email" id="email" name="email" required placeholder=" ">
                            <label for="email">Tu Correo Electrónico</label>
                            <span class="focus-border"></span>
                        </div>
                    </div>

                    <div class="input-group">
                        <input type="text" id="asunto" name="asunto" required placeholder=" ">
                        <label for="asunto">Asunto (ej. Duda sobre Charizard)</label>
                        <span class="focus-border"></span>
                    </div>

                    <div class="input-group">
                        <textarea id="mensaje" name="mensaje" rows="5" required placeholder=" "></textarea>
                        <label for="mensaje">¿En qué podemos ayudarte?</label>
                        <span class="focus-border"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">
                        Enviar Consulta Ahora <span class="arrow">→</span>
                    </button>
                </form>
            </div>

        </div>
    </section>

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
</body>

</html>
