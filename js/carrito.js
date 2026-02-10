// Carrito.js - Lógica del carrito de compras

// Función para añadir al carrito
function addToCart(id, nombre, precio) {
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
            cantidad: 1
        });
    }
    
    localStorage.setItem('puntocarrito', JSON.stringify(carrito));
    alert('Producto: ' + nombre + ' añadido al carrito!');
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
        div.style.borderBottom = '1px solid #444';
        div.style.padding = '10px';
        div.style.display = 'flex';
        div.style.justifyContent = 'space-between';
        
        div.innerHTML = `
            <div>
                <strong>${item.nombre}</strong><br>
                ${item.precio} € x ${item.cantidad}
            </div>
            <div>
                <span>${subtotal.toFixed(2)} €</span>
                <button onclick="eliminarDelCarrito(${index})" style="background:red; color:white; border:none; padding:5px; margin-left:10px; cursor:pointer;">X</button>
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
