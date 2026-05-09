function getCart() {
    try {
        let cart = localStorage.getItem('pokepimas_cart');
        if (cart) {
            let parsed = JSON.parse(cart);
            return Array.isArray(parsed) ? parsed : [];
        }
    } catch (e) {
        console.error("Error al leer el carrito:", e);
    }
    return [];
}

function saveCart(cart) {
    localStorage.setItem('pokepimas_cart', JSON.stringify(cart));
}

function addToCart(id, nombre, precio, imagen = '') {
    try {
        let cart = getCart();
        let existingItem = cart.find(item => item.id == id);

        if (existingItem) {
            existingItem.cantidad += 1;
        } else {
            cart.push({
                id: id,
                nombre: nombre,
                precio: parseFloat(precio),
                imagen: imagen,
                cantidad: 1
            });
        }

        saveCart(cart);
        showToast(`<strong>${nombre}</strong> añadido al carrito`);

        if (document.getElementById('lista-carrito')) {
            mostrarCarrito();
        }
    } catch (e) {
        showToast("Error al añadir al carrito.");
        console.error(e);
    }
}

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

    setTimeout(() => toast.classList.add('show'), 10);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

function mostrarCarrito() {
    let contenedor = document.getElementById('lista-carrito');
    let totalElement = document.getElementById('total-carrito');

    if (!contenedor) return;

    try {
        contenedor.innerHTML = '';
        let carrito = getCart();
        let total = 0;

        if (carrito.length === 0) {
            contenedor.innerHTML = '<p>El carrito está vacío.</p>';
            if (totalElement) totalElement.innerText = '0.00 €';
            return;
        }

        carrito.forEach((item, index) => {
            let precio = parseFloat(item.precio) || 0;
            let subtotal = precio * (item.cantidad || 1);
            total += subtotal;

            let div = document.createElement('div');
            div.classList.add('item-carrito');

            let imgHtml = item.imagen ? `<img src="${item.imagen}" alt="${item.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">` : `<div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📦</div>`;

            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 15px;">
                    ${imgHtml}
                    <div>
                        <strong class="item-name">${item.nombre || 'Producto'}</strong><br>
                        <span class="item-price">${precio.toFixed(2)} € x ${item.cantidad || 1}</span>
                    </div>
                </div>
                <div class="item-actions">
                    <span class="item-subtotal">${subtotal.toFixed(2)} €</span>
                    <button onclick="eliminarDelCarrito(${item.id})" class="btn-remove" title="Eliminar">&times;</button>
                </div>
            `;

            contenedor.appendChild(div);
        });

        if (totalElement) totalElement.innerText = total.toFixed(2) + ' €';
    } catch (e) {
        contenedor.innerHTML = '<p>Error cargando carrito.</p>';
        console.error(e);
    }
}

function eliminarDelCarrito(id) {
    let cart = getCart();
    cart = cart.filter(item => item.id != id);
    saveCart(cart);
    mostrarCarrito();
}

function vaciarCarrito() {
    localStorage.removeItem('pokepimas_cart');
    mostrarCarrito();
}

async function toggleWishlist(id) {
    let formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('id', id);

    try {
        let res = await fetch('api_wishlist.php', { method: 'POST', body: formData });
        let data = await res.json();

        if (data.success) {
            let btn = document.getElementById(`wishlist-btn-${id}`);
            if (data.status === 'added') {
                showToast('Añadido a Wishlist');
                if (btn) btn.innerHTML = '❤️';
            } else {
                showToast('Eliminado de Wishlist');
                if (btn) btn.innerHTML = '🤍';
            }
        } else {
            showToast(data.message || "Error");
        }
    } catch (e) {
        showToast("Error de conexión");
    }
}

document.addEventListener('DOMContentLoaded', () => {
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

    const navContainer = document.querySelector('.nav-container');
    const navLinks = document.querySelector('.nav-links');

    if (navContainer && navLinks && !document.querySelector('.mobile-menu-toggle')) {
        const menuBtn = document.createElement('button');
        menuBtn.className = 'mobile-menu-toggle';
        menuBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
        menuBtn.setAttribute('aria-label', 'Abrir menú de navegación');

        const userActions = document.querySelector('.user-actions');
        if (userActions) {
            navContainer.insertBefore(menuBtn, userActions);
        } else {
            navContainer.appendChild(menuBtn);
        }
        menuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
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

function finalizarCompra() {
    let cart = getCart();
    if (cart.length === 0) {
        showToast("No tienes artículos en el carrito para comprar.");
        return;
    }

    // Crear el overlay
    let overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100vw';
    overlay.style.height = '100vh';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.6)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '9999';
    overlay.style.backdropFilter = 'blur(5px)';

    // Crear el modal
    let modal = document.createElement('div');
    modal.style.backgroundColor = '#fff';
    modal.style.padding = '40px';
    modal.style.borderRadius = '12px';
    modal.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
    modal.style.maxWidth = '400px';
    modal.style.textAlign = 'center';

    modal.innerHTML = `
        <div style="font-size: 40px; margin-bottom: 15px;">🚧</div>
        <h3 style="margin-bottom: 15px; color: #333; font-family: 'Montserrat', sans-serif;">Mantenimiento</h3>
        <p style="color: #666; line-height: 1.5; margin-bottom: 25px; font-family: 'Nunito Sans', sans-serif;">
            El sistema de pago seguro se encuentra temporalmente en mantenimiento o en fase de pruebas. Por favor, inténtelo más tarde. Disculpe las molestias.
        </p>
        <button id="cerrar-modal-btn" class="btn btn-primary" style="width: 100%;">Entendido</button>
    `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    document.getElementById('cerrar-modal-btn').addEventListener('click', () => {
        document.body.removeChild(overlay);
    });
}
