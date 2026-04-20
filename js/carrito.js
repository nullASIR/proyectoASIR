// Carrito.js - Lógica del carrito de compras

// Función para añadir al carrito
function addToCart(id, nombre, precio, imagen = '') {
    let carrito = JSON.parse(localStorage.getItem('puntocarrito')) || [];

    // Verificar si ya existe el producto
    let existente = carrito.find(item => item.id === id);

    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            imagen: imagen,
            cantidad: 1
        });
    }

    localStorage.setItem('puntocarrito', JSON.stringify(carrito));

    // Llamada a la notificación premium en vez del alert nativo
    showToast(`<strong>${nombre}</strong> añadido al carrito`);
}

// ==== Sistema de Notificaciones Toast ====
function showToast(message) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    let toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.innerHTML = `
        <div class="toast-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <span>${message}</span>
    `;

    toastContainer.appendChild(toast);

    // Desencadenar animación de entrada
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto-eliminar después de 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400); // Esperar a que acabe la transición css
    }, 3000);
}

// Función para mostrar el carrito (usada en ver_carrito.php)
function mostrarCarrito() {
    let carrito = JSON.parse(localStorage.getItem('puntocarrito')) || [];
    let contenedor = document.getElementById('lista-carrito');
    let totalElement = document.getElementById('total-carrito');
    let total = 0;

    contenedor.innerHTML = '';

    if (carrito.length === 0) {
        contenedor.innerHTML = '<p>El carrito está vacío.</p>';
        totalElement.innerText = '0.00 €';
        return;
    }

    carrito.forEach((item, index) => {
        let subtotal = item.precio * item.cantidad;
        total += subtotal;

        let div = document.createElement('div');
        div.classList.add('item-carrito');

        let imgHtml = item.imagen ? `<img src="${item.imagen}" alt="${item.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">` : `<div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📦</div>`;

        div.innerHTML = `
            <div style="display: flex; align-items: center; gap: 15px;">
                ${imgHtml}
                <div>
                    <strong class="item-name">${item.nombre}</strong><br>
                    <span class="item-price">${item.precio} € x ${item.cantidad}</span>
                </div>
            </div>
            <div class="item-actions">
                <span class="item-subtotal">${subtotal.toFixed(2)} €</span>
                <button onclick="eliminarDelCarrito(${index})" class="btn-remove" title="Eliminar">&times;</button>
            </div>
        `;

        contenedor.appendChild(div);
    });

    totalElement.innerText = total.toFixed(2) + ' €';
}

function eliminarDelCarrito(index) {
    let carrito = JSON.parse(localStorage.getItem('puntocarrito')) || [];
    carrito.splice(index, 1);
    localStorage.setItem('puntocarrito', JSON.stringify(carrito));
    mostrarCarrito();
}

function vaciarCarrito() {
    localStorage.removeItem('puntocarrito');
    mostrarCarrito();
}

// ==== Footer Acordeón Móvil (Menú desplegable) ====
document.addEventListener('DOMContentLoaded', () => {
    // Lógica Footer
    const footerHeaders = document.querySelectorAll('.footer-links h4');
    footerHeaders.forEach(header => {
        header.addEventListener('click', () => {
            if (window.innerWidth <= 1150) {
                const parent = header.parentElement;
                document.querySelectorAll('.footer-links').forEach(link => {
                    if (link !== parent) link.classList.remove('active');
                });
                parent.classList.toggle('active');
            }
        });
    });

    // ==== Navbar Desplegable Móvil ====
    const navContainer = document.querySelector('.nav-container');
    const navLinks = document.querySelector('.nav-links');

    if (navContainer && navLinks && !document.querySelector('.mobile-menu-toggle')) {
        // Crear botón Hamburguesa
        const menuBtn = document.createElement('button');
        menuBtn.className = 'mobile-menu-toggle';
        menuBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
        menuBtn.setAttribute('aria-label', 'Abrir menú de navegación');

        // Insertarlo antes de las acciones de usuario
        const userActions = document.querySelector('.user-actions');
        if (userActions) {
            navContainer.insertBefore(menuBtn, userActions);
        } else {
            navContainer.appendChild(menuBtn);
        }

        // Toggle del menú
        menuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            // Animación y rotación de estado
            if (navLinks.classList.contains('active')) {
                menuBtn.classList.add('is-active');
                menuBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            } else {
                menuBtn.classList.remove('is-active');
                menuBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
            }
        });
    }
});
