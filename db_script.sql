CREATE DATABASE IF NOT EXISTS Pimas;
USE Pimas;

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

CREATE TABLE User (
    Id INTEGER PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Mail VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Verified BOOLEAN NOT NULL DEFAULT FALSE,
    
    VerificationCode VARCHAR(10) DEFAULT NULL,
    VerificationExpires DATETIME DEFAULT NULL,
    
    ResetToken VARCHAR(255) DEFAULT NULL,
    ResetExpires DATETIME DEFAULT NULL,
    
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

CREATE TABLE Wishlist (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    ProductoId INT,
    FOREIGN KEY (UserId) REFERENCES User(Id),
    FOREIGN KEY (ProductoId) REFERENCES Productos(IdProducto)
) ENGINE=InnoDB;

INSERT INTO User (Name, Mail, Password, Verified, IsAdmin) VALUES
('Admin Pimas', 'admin@pimas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, TRUE);

INSERT INTO Productos (Ean, Nombre, Precio, Stock, Estado, Impuesto, Imagen, Tipo) VALUES
(123456789, 'Charizard Base Set', 150.00, 5, 'Nuevo', 21.00, 'https://storage.googleapis.com/images.pricecharting.com/0e3bbf4bbd5b02a86e496ede579a072a5fa51c34136cb85cb5d3222e4d11dc9b/1600.jpg','Carta Suelta'),
(987654321, 'Blastoise Base Set', 100.00, 10, 'Usado', 21.00, 'https://images.wikidexcdn.net/mwuploads/wikidex/thumb/3/3a/latest/20240811222606/Blastoise_%28Base_Set_TCG%29.png/250px-Blastoise_%28Base_Set_TCG%29.png','Carta Suelta'),
(4521098765432, 'Caja Sobres Escarlata y Púrpura', '120.00', '50', 'Nuevo', 'https://arte9.com/wp-content/uploads/2023/03/ESCARLATA-Y-PU%CC%81RPURA-CAJA.jpg', '21.00', 'Producto Sellado'),
(4521098765433, 'Fundas Protectoras Ultra Pro', '5.99', '200', 'Nuevo', 'https://m.media-amazon.com/images/I/91D4xpH7cgL.jpg', '21.00', 'Accesorio'),
(4521098765434, 'Carpeta Pokémon 9 Bolsillos', '15.50', '80', 'Nuevo', 'https://m.media-amazon.com/images/I/81t8iT1SxOL._AC_UF1000,1000_QL80_.jpg', '21.00', 'Accesorio'),
(4521098765435, 'Pikachu Gold Star', '250.00', '2', 'Usado', 'https://storage.googleapis.com/images.pricecharting.com/3874eafb1668ec96a5359d803d6fc3203c2918234f39e679591cd20837f17cc1/1600.jpg', '21.00', 'Carta Suelta');