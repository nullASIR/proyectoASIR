CREATE DATABASE IF NOT EXISTS pimas;
USE pimas;

-- 1. Tablas independientes (sin llaves foráneas)
CREATE TABLE address (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    street VARCHAR(100),
    country VARCHAR(100),
    state VARCHAR(100),
    city VARCHAR(100),
    postalCode INTEGER
);

CREATE TABLE productos (
    id_producto INTEGER PRIMARY KEY AUTO_INCREMENT,
    ean INTEGER,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    stock INTEGER,
    estado VARCHAR(100),
    impuesto DECIMAL(10,2),
    tipo VARCHAR(100)
);

CREATE TABLE proveedores (
    id_proveedor INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    contacto VARCHAR(100),
    correo VARCHAR(100)
);

-- 2. Tablas con dependencias simples
CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    mail VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT FALSE,
    addressId INTEGER,
    failed_attempts INT DEFAULT 0,
    lockout_time DATETIME NULL,
    FOREIGN KEY (addressId) REFERENCES address(id)
);

CREATE TABLE pagos (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    metodo VARCHAR(100),
    estado VARCHAR(100),
    user_id INTEGER,
    cantidad DECIMAL(10,2), 
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- 3. Tabla de pedidos (depende de user, address y pagos)
CREATE TABLE pedido (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    address_id INTEGER,
    pago_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (address_id) REFERENCES address(id),
    FOREIGN KEY (pago_id) REFERENCES pagos(id)
);

-- 4. Tablas intermedias / Relaciones N:M
CREATE TABLE productos_proveedores (
    id_producto INTEGER,
    id_proveedor INTEGER,
    fecha_asociacion DATE,
    PRIMARY KEY (id_producto, id_proveedor),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
);

CREATE TABLE productosPedido (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pedidoId INTEGER,
    idProductos INTEGER,
    precioUnidad DECIMAL(10,2),
    cantidad INTEGER,
    precioLinea DECIMAL(10,2),
    FOREIGN KEY (pedidoId) REFERENCES pedido(id),
    FOREIGN KEY (idProductos) REFERENCES productos(id_producto)
);

-- Datos de ejemplo
INSERT INTO user (name, mail, password, verified) VALUES
('Admin Pimas', 'admin@pimas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
('Cliente 1', 'cliente1@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

INSERT INTO productos (ean, nombre, precio, stock, estado, impuesto, tipo) VALUES
(123456789, 'Charizard Base Set', 150.00, 5, 'Nuevo', 21.00, 'Carta Suelta'),
(987654321, 'Blastoise Base Set', 100.00, 10, 'Usado', 21.00, 'Carta Suelta'),
(111222333, 'Caja de Sobres XY', 120.00, 20, 'Nuevo', 21.00, 'Caja Sellada'),
(444555666, 'Elite Trainer Box', 45.00, 15, 'Nuevo', 21.00, 'Accesorio');
