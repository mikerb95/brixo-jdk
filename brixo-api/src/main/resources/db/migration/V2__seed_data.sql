-- ═══════════════════════════════════════════════════════════════════
-- Brixo — V2: Datos iniciales (seed) desde el sistema legacy
-- Categorías, servicios, ubicaciones, y un admin por defecto
-- Contraseña por defecto: "password" (bcrypt hash)
-- ═══════════════════════════════════════════════════════════════════

-- El hash bcrypt de "password" — generado con BCryptPasswordEncoder (cost 10)
SET @pwd = '$2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy';

-- ──────────────────────────────────────
-- Categorías
-- ──────────────────────────────────────
INSERT INTO CATEGORIA (nombre, descripcion, imagen_url) VALUES
('Hogar',         'Servicios generales para el hogar',              '/images/categorias/hogar.jpg'),
('Construcción',  'Obras civiles y remodelaciones',                 '/images/categorias/construccion.jpg'),
('Plomería',      'Instalación y reparación de tuberías y grifos',  '/images/categorias/plomeria.jpg'),
('Electricidad',  'Servicios eléctricos residenciales y comerciales', '/images/categorias/electricidad.jpg'),
('Limpieza',      'Servicios de aseo residencial y comercial',      '/images/categorias/limpieza.jpg');

-- ──────────────────────────────────────
-- Servicios
-- ──────────────────────────────────────
INSERT INTO SERVICIO (nombre, descripcion, precio_estimado, imagen_url, id_categoria) VALUES
('Pintura interior',      'Pintura de paredes y techos interiores', 150000.00, '/images/servicios/pintura.jpg',      1),
('Remodelación de baño',  'Remodelación completa de baño',         800000.00, '/images/servicios/bano.jpg',          2),
('Reparación de grifos',  'Reparación o cambio de grifería',        80000.00, '/images/servicios/grifo.jpg',         3),
('Instalación eléctrica', 'Cableado y puntos eléctricos nuevos',   200000.00, '/images/servicios/electrica.jpg',     4),
('Limpieza profunda',     'Aseo profundo de hogar o local',        120000.00, '/images/servicios/limpieza.jpg',      5);

-- ──────────────────────────────────────
-- Ubicaciones (Bogotá)
-- ──────────────────────────────────────
INSERT INTO UBICACION (ciudad, departamento, direccion, latitud, longitud) VALUES
('Bogotá', 'Cundinamarca', 'Chapinero',       4.64920000, -74.06290000),
('Bogotá', 'Cundinamarca', 'Usaquén',         4.69580000, -74.03200000),
('Bogotá', 'Cundinamarca', 'Suba',            4.74140000, -74.08340000),
('Bogotá', 'Cundinamarca', 'Teusaquillo',     4.63460000, -74.08210000),
('Bogotá', 'Cundinamarca', 'Kennedy',         4.62820000, -74.14900000),
('Bogotá', 'Cundinamarca', 'Fontibón',        4.67500000, -74.14090000),
('Bogotá', 'Cundinamarca', 'Engativá',        4.70520000, -74.10980000),
('Bogotá', 'Cundinamarca', 'Barrios Unidos',  4.66860000, -74.07600000),
('Bogotá', 'Cundinamarca', 'Santa Fe',        4.60270000, -74.06640000),
('Bogotá', 'Cundinamarca', 'La Candelaria',   4.59640000, -74.07340000),
('Bogotá', 'Cundinamarca', 'San Cristóbal',   4.57310000, -74.08850000),
('Bogotá', 'Cundinamarca', 'Rafael Uribe',    4.57170000, -74.11230000),
('Bogotá', 'Cundinamarca', 'Antonio Nariño',  4.58690000, -74.10410000),
('Bogotá', 'Cundinamarca', 'Puente Aranda',   4.62310000, -74.11510000),
('Bogotá', 'Cundinamarca', 'Los Mártires',    4.61000000, -74.08790000);

-- ──────────────────────────────────────
-- Admin por defecto
-- ──────────────────────────────────────
INSERT INTO ADMIN (nombre, correo, contrasena, activo) VALUES
('Administrador', 'admin@brixo.com', @pwd, 1);
