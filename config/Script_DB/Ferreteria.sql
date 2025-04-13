CREATE DATABASE IF NOT EXISTS ferreteria;
USE ferreteria;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    categoria VARCHAR(50),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES
('Taladro percutor 750W', 'Taladro eléctrico con percusión para perforar hormigón.', 45.99, 30, 'Herramientas eléctricas'),
('Destornillador eléctrico', 'Destornillador recargable, incluye 10 puntas intercambiables.', 22.50, 50, 'Herramientas eléctricas'),
('Sierra de calar', 'Sierra de calar 600W, incluye 3 hojas para madera y metal.', 55.99, 15, 'Herramientas eléctricas'),
('Cinta métrica 5 metros', 'Cinta métrica de alta precisión con gancho retráctil.', 7.99, 100, 'Medición'),
('Martillo de goma', 'Martillo con cabezal de goma ideal para trabajos delicados.', 9.99, 80, 'Herramientas manuales'),
('Caja de herramientas completa', 'Caja de herramientas con 100 piezas, incluyendo destornilladores, llaves, etc.', 59.99, 25, 'Accesorios');

