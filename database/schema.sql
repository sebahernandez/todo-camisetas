-- Base de datos para Todo Camisetas API
-- Examen Transversal Final - Desarrollo Backend

CREATE DATABASE IF NOT EXISTS todo_camisetas;
USE todo_camisetas;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') DEFAULT 'user',
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de marcas
CREATE TABLE IF NOT EXISTS marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de camisetas
CREATE TABLE IF NOT EXISTS camisetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    talla ENUM('XS', 'S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    color VARCHAR(100),
    stock INT DEFAULT 0,
    imagen VARCHAR(500),
    categoria_id INT NOT NULL,
    marca_id INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE CASCADE
);

-- Índices para mejorar rendimiento
CREATE INDEX idx_camisetas_categoria ON camisetas(categoria_id);
CREATE INDEX idx_camisetas_marca ON camisetas(marca_id);
CREATE INDEX idx_camisetas_precio ON camisetas(precio);
CREATE INDEX idx_camisetas_talla ON camisetas(talla);
CREATE INDEX idx_camisetas_activo ON camisetas(activo);

-- Datos de ejemplo

-- NOTA: Debes crear un usuario administrador manualmente
-- Ejemplo de sentencia (reemplazar con un hash de contraseña seguro):
-- INSERT INTO usuarios (email, password, nombre, rol) VALUES 
-- ('admin@example.com', 'HASH_PASSWORD_SEGURO', 'Administrador', 'admin');

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion) VALUES 
('Deportivas', 'Camisetas para deportes y actividades físicas'),
('Casuales', 'Camisetas para uso diario y casual'),
('Formales', 'Camisetas para ocasiones formales'),
('Vintage', 'Camisetas con diseños retro y vintage'),
('Estampadas', 'Camisetas con estampados y diseños únicos');

-- Insertar marcas
INSERT INTO marcas (nombre, descripcion) VALUES 
('Nike', 'Marca deportiva líder mundial'),
('Adidas', 'Marca alemana de ropa deportiva'),
('Puma', 'Marca deportiva multinacional'),
('Zara', 'Marca española de moda'),
('H&M', 'Marca sueca de ropa casual'),
('Lacoste', 'Marca francesa de ropa deportiva elegante'),
('Polo Ralph Lauren', 'Marca americana de ropa premium');

-- Insertar camisetas de ejemplo
INSERT INTO camisetas (nombre, descripcion, precio, talla, color, stock, categoria_id, marca_id) VALUES 
('Camiseta Nike Dri-FIT', 'Camiseta deportiva con tecnología de absorción de humedad', 45990, 'M', 'Negro', 25, 1, 1),
('Camiseta Adidas Originals', 'Camiseta casual con logo clásico de Adidas', 39990, 'L', 'Blanco', 30, 2, 2),
('Camiseta Puma Essential', 'Camiseta básica de algodón 100%', 29990, 'S', 'Azul', 20, 2, 3),
('Camiseta Zara Básica', 'Camiseta de corte regular para uso diario', 19990, 'M', 'Gris', 40, 2, 4),
('Camiseta H&M Estampada', 'Camiseta con estampado gráfico moderno', 24990, 'L', 'Verde', 15, 5, 5),
('Polo Lacoste Classic', 'Polo clásico con logo de cocodrilo', 89990, 'M', 'Azul Marino', 10, 3, 6),
('Camiseta Ralph Lauren', 'Camiseta premium con bordado del logo', 69990, 'L', 'Blanco', 12, 3, 7),
('Nike Air Max Tee', 'Camiseta conmemorativa de la línea Air Max', 49990, 'XL', 'Rojo', 18, 5, 1),
('Adidas 3-Stripes', 'Camiseta con las icónicas tres rayas', 34990, 'S', 'Negro', 22, 1, 2),
('Puma Vintage Logo', 'Camiseta con logo vintage de Puma', 35990, 'M', 'Amarillo', 16, 4, 3);
