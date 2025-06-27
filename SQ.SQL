-- ===============================================
-- EJERCICIO POSTGRESQL: TABLAS, DATOS Y VISTAS
-- ===============================================

-- PARTE 1: CREACIÓN DE TABLAS
-- ===============================================

-- Tabla clientes
CREATE TABLE clientes (
    idclientes SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    direccion VARCHAR(200),
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla productos
CREATE TABLE productos (
    idproductos SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio NUMERIC(10,2) NOT NULL
);

-- Tabla pedidos
CREATE TABLE pedidos (
    idpedidos SERIAL PRIMARY KEY,
    idcliente INTEGER NOT NULL,
    idproducto INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    fechapedido DATE NOT NULL,
    FOREIGN KEY (idcliente) REFERENCES clientes(idclientes),
    FOREIGN KEY (idproducto) REFERENCES productos(idproductos)
);

-- PARTE 2: INSERCIÓN DE DATOS
-- ===============================================

-- Insertar datos en la tabla clientes
INSERT INTO clientes (nombre, apellido, direccion, activo) VALUES
('Juan', 'Pérez', 'Av. Principal 123, Santiago', TRUE),
('María', 'González', 'Calle Los Robles 456, Valparaíso', TRUE),
('Pedro', 'Martínez', 'Pasaje Las Flores 789, Concepción', FALSE),
('Ana', 'López', 'Av. Libertad 321, La Serena', TRUE),
('Carlos', 'Rodríguez', 'Calle Central 654, Temuco', FALSE),
('Sofía', 'Hernández', 'Av. O\'Higgins 987, Rancagua', TRUE);

-- Insertar datos en la tabla productos
INSERT INTO productos (nombre, precio) VALUES
('Laptop Dell Inspiron', 799990.00),
('Mouse Inalámbrico Logitech', 25990.00),
('Teclado Mecánico', 89990.00),
('Monitor 24 pulgadas', 199990.00),
('Auriculares Bluetooth', 49990.00),
('Disco Duro Externo 1TB', 69990.00);

-- Insertar datos en la tabla pedidos
INSERT INTO pedidos (idcliente, idproducto, cantidad, fechapedido) VALUES
(1, 1, 1, '2024-01-15'),
(1, 2, 2, '2024-01-15'),
(2, 3, 1, '2024-01-20'),
(2, 4, 1, '2024-01-25'),
(4, 1, 1, '2024-02-01'),
(4, 5, 2, '2024-02-01'),
(6, 2, 3, '2024-02-10'),
(6, 6, 1, '2024-02-15'),
(1, 4, 1, '2024-02-20'),
(2, 5, 1, '2024-02-25');

-- PARTE 3: CREACIÓN DE VISTAS
-- ===============================================

-- 3.1. Vista para Mostrar Todos los Clientes con Nombre Completo y Estado
CREATE VIEW vw_clientesvigentes AS
SELECT 
    idclientes,
    CONCAT(nombre, ' ', apellido) AS nombrecompleto,
    CASE 
        WHEN activo = TRUE THEN 'ACTIVO'
        ELSE 'NO ACTIVO'
    END AS vigente,
    direccion
FROM clientes;

-- 3.2. Vista para Mostrar los Detalles de los Pedidos
CREATE VIEW vw_detallespedidos AS
SELECT 
    p.idpedidos AS idpedido,
    CONCAT(c.nombre, ' ', c.apellido) AS nombrecliente,
    pr.nombre AS nombreproducto,
    p.cantidad,
    p.fechapedido
FROM pedidos p
JOIN clientes c ON p.idcliente = c.idclientes
JOIN productos pr ON p.idproducto = pr.idproductos;

-- 3.3. Vista para Calcular el Total de Ventas por Producto
CREATE VIEW vw_totalventasporproducto AS
SELECT 
    pr.idproductos AS idproducto,
    pr.nombre AS nombreproducto,
    SUM(p.cantidad * pr.precio) AS totalventas
FROM productos pr
JOIN pedidos p ON pr.idproductos = p.idproducto
GROUP BY pr.idproductos, pr.nombre;

-- 3.4. Vista para Mostrar la Cantidad de Veces que Cada Producto Ha Sido Vendido
CREATE VIEW vw_ventasporproducto AS
SELECT 
    pr.idproductos AS idproducto,
    pr.nombre AS nombreproducto,
    SUM(p.cantidad) AS cantidadvendida
FROM productos pr
JOIN pedidos p ON pr.idproductos = p.idproducto
GROUP BY pr.idproductos, pr.nombre;

-- 3.5. Vista para Mostrar los Pedidos por Cliente Activo
CREATE VIEW vw_pedidos_clientes_activos AS
SELECT 
    p.idpedidos AS idpedido,
    CONCAT(c.nombre, ' ', c.apellido) AS nombrecliente,
    pr.nombre AS nombreproducto,
    p.cantidad,
    p.fechapedido
FROM pedidos p
JOIN clientes c ON p.idcliente = c.idclientes
JOIN productos pr ON p.idproducto = pr.idproductos
WHERE c.activo = TRUE;

-- 3.6. Vista para Mostrar los Productos que No Han Sido Vendidos
CREATE VIEW vw_productos_no_vendidos AS
SELECT 
    pr.idproductos AS idproducto,
    pr.nombre AS nombreproducto,
    pr.precio
FROM productos pr
LEFT JOIN pedidos p ON pr.idproductos = p.idproducto
WHERE p.idproducto IS NULL;

-- 3.7. Vista para Mostrar los Clientes que Han Realizado Pedidos
CREATE VIEW vw_clientes_con_pedidos AS
SELECT 
    c.idclientes AS idcliente,
    CONCAT(c.nombre, ' ', c.apellido) AS nombrecliente,
    COUNT(p.idpedidos) AS totalpedidos
FROM clientes c
JOIN pedidos p ON c.idclientes = p.idcliente
GROUP BY c.idclientes, c.nombre, c.apellido;

-- ===============================================
-- CONSULTAS DE VERIFICACIÓN
-- ===============================================

-- Verificar datos insertados
SELECT 'CLIENTES' as tabla, COUNT(*) as total_registros FROM clientes
UNION ALL
SELECT 'PRODUCTOS' as tabla, COUNT(*) as total_registros FROM productos
UNION ALL
SELECT 'PEDIDOS' as tabla, COUNT(*) as total_registros FROM pedidos;

-- Probar las vistas creadas
SELECT 'Vista: Clientes Vigentes' as descripcion;
SELECT * FROM vw_clientesvigentes;

SELECT 'Vista: Detalles de Pedidos' as descripcion;
SELECT * FROM vw_detallespedidos;

SELECT 'Vista: Total Ventas por Producto' as descripcion;
SELECT * FROM vw_totalventasporproducto;

SELECT 'Vista: Ventas por Producto' as descripcion;
SELECT * FROM vw_ventasporproducto;

SELECT 'Vista: Pedidos de Clientes Activos' as descripcion;
SELECT * FROM vw_pedidos_clientes_activos;

SELECT 'Vista: Productos No Vendidos' as descripcion;
SELECT * FROM vw_productos_no_vendidos;

SELECT 'Vista: Clientes con Pedidos' as descripcion;
SELECT * FROM vw_clientes_con_pedidos;