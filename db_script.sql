-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS Pimas;
USE Pimas;

-- 1. TABLAS INDEPENDIENTES
-- -----------------------------------------------------
CREATE TABLE Address (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    Street VARCHAR(100),
    Country VARCHAR(100),
    State VARCHAR(100),
    City VARCHAR(100),
    PostalCode INTEGER
) ENGINE=InnoDB;

CREATE TABLE Productos (
    IdProducto INTEGER PRIMARY KEY AUTO_INCREMENT,
    Ean BIGINT, 
    Nombre VARCHAR(100),
    Precio DECIMAL(10,2),
    Stock INTEGER,
    Estado VARCHAR(100),
    Impuesto DECIMAL(10,2),
    Imagen VARCHAR(100),
    Tipo VARCHAR(100),
    INDEX IdxNombreProducto (Nombre)
) ENGINE=InnoDB;

CREATE TABLE Proveedores (
    IdProveedor INTEGER PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100),
    Contacto VARCHAR(100),
    Correo VARCHAR(100)
) ENGINE=InnoDB;

-- 2. TABLAS CON DEPENDENCIAS (USUARIOS Y PAGOS)
-- -----------------------------------------------------
CREATE TABLE User (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Mail VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Verified BOOLEAN NOT NULL DEFAULT FALSE,
    
    -- Campos para Verificación de Cuenta (PascalCase)
    VerificationCode VARCHAR(10) DEFAULT NULL,
    VerificationExpires DATETIME DEFAULT NULL,
    
    -- Campos para Recuperación de Contraseña (PascalCase)
    ResetToken VARCHAR(255) DEFAULT NULL,
    ResetExpires DATETIME DEFAULT NULL,
    
    -- Campos de Seguridad y Auditoría
    FailedAttempts INT DEFAULT 0,
    LockoutTime DATETIME NULL,
    AddressId INTEGER,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IsAdmin BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (AddressId) REFERENCES Address(Id)
) ENGINE=InnoDB;

CREATE TABLE Pagos (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    Metodo VARCHAR(100),
    Estado VARCHAR(100),
    UserId INTEGER,
    Cantidad DECIMAL(10,2), 
    FOREIGN KEY (UserId) REFERENCES User(Id)
) ENGINE=InnoDB;

-- 3. GESTIÓN DE PEDIDOS
-- -----------------------------------------------------
CREATE TABLE Pedido (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    UserId INTEGER,
    AddressId INTEGER,
    PagoId INTEGER,
    FechaPedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserId) REFERENCES User(Id),
    FOREIGN KEY (AddressId) REFERENCES Address(Id),
    FOREIGN KEY (PagoId) REFERENCES Pagos(Id)
) ENGINE=InnoDB;

-- 4. RELACIONES MUCHOS A MUCHOS (N:M)
-- -----------------------------------------------------
CREATE TABLE ProductosProveedores (
    IdProducto INTEGER,
    IdProveedor INTEGER,
    FechaAsociacion DATE,
    PRIMARY KEY (IdProducto, IdProveedor),
    FOREIGN KEY (IdProducto) REFERENCES Productos(IdProducto),
    FOREIGN KEY (IdProveedor) REFERENCES Proveedores(IdProveedor)
) ENGINE=InnoDB;

CREATE TABLE ProductosPedido (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    PedidoId INTEGER,
    IdProductos INTEGER,
    PrecioUnidad DECIMAL(10,2),
    Cantidad INTEGER,
    PrecioLinea DECIMAL(10,2),
    FOREIGN KEY (PedidoId) REFERENCES Pedido(Id),
    FOREIGN KEY (IdProductos) REFERENCES Productos(IdProducto)
) ENGINE=InnoDB;

-- 5. CARRITO Y WISHLIST
-- -----------------------------------------------------
CREATE TABLE Cart (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    FOREIGN KEY (UserId) REFERENCES User(Id)
) ENGINE=InnoDB;

CREATE TABLE CartItem (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    CartId INT,
    ProductoId INT,
    Cantidad INT,
    FOREIGN KEY (CartId) REFERENCES Cart(Id),
    FOREIGN KEY (ProductoId) REFERENCES Productos(IdProducto)
) ENGINE=InnoDB;

CREATE TABLE Wishlist (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    ProductoId INT,
    FOREIGN KEY (UserId) REFERENCES User(Id),
    FOREIGN KEY (ProductoId) REFERENCES Productos(IdProducto)
) ENGINE=InnoDB;

-- 6. DATOS DE EJEMPLO
-- -----------------------------------------------------
INSERT INTO User (Name, Mail, Password, Verified, IsAdmin) VALUES
('Admin Pimas', 'admin@pimas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, TRUE);

INSERT INTO Productos (Ean, Nombre, Precio, Stock, Estado, Impuesto, Tipo) VALUES
(123456789, 'Charizard Base Set', 150.00, 5, 'Nuevo', 21.00, 'Carta Suelta'),
(987654321, 'Blastoise Base Set', 100.00, 10, 'Usado', 21.00, 'Carta Suelta');