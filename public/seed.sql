-- Seed compatible con el esquema Legacy + Features
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tablas
TRUNCATE TABLE RESENA;
TRUNCATE TABLE CONTRATO;
TRUNCATE TABLE CONTRATISTA_SERVICIO;
TRUNCATE TABLE CONTRATISTA_UBICACION;
TRUNCATE TABLE SERVICIO;
TRUNCATE TABLE CATEGORIA;
TRUNCATE TABLE UBICACION;
TRUNCATE TABLE CONTRATISTA;
TRUNCATE TABLE CLIENTE;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Categorías
INSERT INTO CATEGORIA (id_categoria, nombre, descripcion, imagen_url) VALUES
(1, 'Hogar', 'Servicios generales para el hogar', 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=500&q=60'),
(2, 'Construcción', 'Obras y remodelaciones', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=500&q=60'),
(3, 'Plomería', 'Reparación e instalación de tuberías', 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=500&q=60'),
(4, 'Electricidad', 'Instalaciones y reparaciones eléctricas', 'https://images.unsplash.com/photo-1621905251189-fcfa35257645?auto=format&fit=crop&w=500&q=60'),
(5, 'Limpieza', 'Servicios de limpieza profesional', 'https://images.unsplash.com/photo-1581578731117-104f2a863a30?auto=format&fit=crop&w=500&q=60');

-- 2. Servicios
INSERT INTO SERVICIO (id_servicio, nombre, descripcion, precio_estimado, imagen_url, id_categoria) VALUES
(1, 'Limpieza General', 'Limpieza profunda de casas y apartamentos', 50000.00, 'https://images.unsplash.com/photo-1584622050111-993a426fbf0a?auto=format&fit=crop&w=500&q=60', 5),
(2, 'Reparación de Tuberías', 'Arreglo de fugas y tuberías rotas', 80000.00, 'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?auto=format&fit=crop&w=500&q=60', 3),
(3, 'Instalación Eléctrica', 'Cableado y puntos de luz', 120000.00, 'https://images.unsplash.com/photo-1558346490-a72e53ae2d4f?auto=format&fit=crop&w=500&q=60', 4),
(4, 'Pintura de Interiores', 'Pintura de paredes y techos', 25000.00, 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&w=500&q=60', 1),
(5, 'Remodelación de Baños', 'Cambio de enchapes y sanitarios', 1500000.00, 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=500&q=60', 2);

-- 3. Clientes (Contraseña: password)
INSERT INTO CLIENTE (nombre, correo, contrasena, telefono, foto_perfil) VALUES
('Juan Pérez', 'juan.perez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3001234567', 'https://randomuser.me/api/portraits/men/1.jpg'),
('Maria Gomez', 'maria.gomez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3007654321', 'https://randomuser.me/api/portraits/women/2.jpg'),
('Carlos Lopez', 'carlos.lopez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3101112233', 'https://randomuser.me/api/portraits/men/3.jpg'),
('Laura Fernandez', 'laura.fernandez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3002223344', 'https://randomuser.me/api/portraits/women/8.jpg'),
('Andres Rivera', 'andres.rivera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3015556677', 'https://randomuser.me/api/portraits/men/9.jpg'),
('Natalia Castro', 'natalia.castro@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3021118899', 'https://randomuser.me/api/portraits/women/10.jpg'),
('Felipe Duarte', 'felipe.duarte@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3153332211', 'https://randomuser.me/api/portraits/men/11.jpg'),
('Camila Rojas', 'camila.rojas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3164443322', 'https://randomuser.me/api/portraits/women/12.jpg'),
('Daniela Silva', 'daniela.silva@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3175554433', 'https://randomuser.me/api/portraits/women/13.jpg'),
('Javier Ortiz', 'javier.ortiz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3186665544', 'https://randomuser.me/api/portraits/men/14.jpg'),
('Paula Mejia', 'paula.mejia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3197776655', 'https://randomuser.me/api/portraits/women/15.jpg'),
('Sergio Alvarez', 'sergio.alvarez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3208887766', 'https://randomuser.me/api/portraits/men/15.jpg'),
('Carolina Pardo', 'carolina.pardo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3219998877', 'https://randomuser.me/api/portraits/women/16.jpg'),
('Ricardo Molina', 'ricardo.molina@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3220009988', 'https://randomuser.me/api/portraits/men/16.jpg'),
('Monica Herrera', 'monica.herrera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3231110099', 'https://randomuser.me/api/portraits/women/17.jpg'),
('Oscar Gil', 'oscar.gil@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3242221100', 'https://randomuser.me/api/portraits/men/17.jpg'),
('Valeria Suarez', 'valeria.suarez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3253332210', 'https://randomuser.me/api/portraits/women/18.jpg'),
('Hernan Rios', 'hernan.rios@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3264443320', 'https://randomuser.me/api/portraits/men/18.jpg'),
('Patricia Vega', 'patricia.vega@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3275554430', 'https://randomuser.me/api/portraits/women/19.jpg'),
('Diego Campos', 'diego.campos@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3286665540', 'https://randomuser.me/api/portraits/men/19.jpg'),
('Luisa Acosta', 'luisa.acosta@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3297776650', 'https://randomuser.me/api/portraits/women/20.jpg');

-- 4. Contratistas (Contraseña: password)
INSERT INTO CONTRATISTA (nombre, correo, contrasena, telefono, ciudad, ubicacion_mapa, foto_perfil, experiencia, portafolio, descripcion_perfil, verificado) VALUES
('Pedro Rodriguez', 'pedro.rodriguez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3209998877', 'Bogotá', '4.710989,-74.072090', 'https://randomuser.me/api/portraits/men/4.jpg', '10 años en plomería', 'https://portfolio.example.com/pedro', 'Experto en reparaciones urgentes', 1),
('Ana Martinez', 'ana.martinez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3155556677', 'Bogotá', '4.712500,-74.070000', 'https://randomuser.me/api/portraits/women/5.jpg', '5 años en diseño de interiores y pintura', 'https://portfolio.example.com/ana', 'Transformo espacios con color', 1),
('Luis Hernandez', 'luis.hernandez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3004443322', 'Bogotá', '4.709000,-74.075000', 'https://randomuser.me/api/portraits/men/6.jpg', 'Ingeniero eléctrico certificado', 'https://portfolio.example.com/luis', 'Seguridad y eficiencia eléctrica', 1),
('Sofia Ramirez', 'sofia.ramirez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3112223344', 'Bogotá', '4.715000,-74.080000', 'https://randomuser.me/api/portraits/women/7.jpg', 'Especialista en limpieza profunda', 'https://portfolio.example.com/sofia', 'Tu casa impecable en horas', 0),
('Jorge Torres', 'jorge.torres@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3123334455', 'Bogotá', '4.705000,-74.065000', 'https://randomuser.me/api/portraits/men/8.jpg', 'Maestro de obra', 'https://portfolio.example.com/jorge', 'Construcción y remodelación garantizada', 1),
('Mariana Lopez', 'mariana.lopez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3001112234', 'Bogotá', '4.720000,-74.060000', 'https://randomuser.me/api/portraits/women/21.jpg', '7 años en limpieza profesional', 'https://portfolio.example.com/mariana', 'Limpieza detallada para tu hogar', 1),
('Andres Torres', 'andres.torres@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3012223345', 'Bogotá', '4.725000,-74.085000', 'https://randomuser.me/api/portraits/men/21.jpg', '5 años en plomería residencial', 'https://portfolio.example.com/andres', 'Soluciones rápidas y efectivas', 1),
('Juliana Ruiz', 'juliana.ruiz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3023334456', 'Bogotá', '4.708000,-74.085000', 'https://randomuser.me/api/portraits/women/22.jpg', 'Diseño y pintura de interiores', 'https://portfolio.example.com/juliana', 'Espacios modernos y acogedores', 1),
('Mateo Vargas', 'mateo.vargas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3034445567', 'Bogotá', '4.700000,-74.070000', 'https://randomuser.me/api/portraits/men/22.jpg', 'Electricista certificado', 'https://portfolio.example.com/mateo', 'Instalaciones seguras y eficientes', 1),
('Sara Lozano', 'sara.lozano@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3045556678', 'Bogotá', '4.718000,-74.078000', 'https://randomuser.me/api/portraits/women/23.jpg', 'Experta en organización y limpieza', 'https://portfolio.example.com/sara', 'Hogares ordenados y limpios', 1),
('Nicolas Pineda', 'nicolas.pineda@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3056667789', 'Bogotá', '4.713000,-74.060000', 'https://randomuser.me/api/portraits/men/23.jpg', 'Remodelaciones integrales', 'https://portfolio.example.com/nicolas', 'Transformo tus espacios', 1),
('Angela Carrillo', 'angela.carrillo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3067778890', 'Bogotá', '4.706000,-74.078500', 'https://randomuser.me/api/portraits/women/24.jpg', 'Pintura decorativa', 'https://portfolio.example.com/angela', 'Paredes con personalidad', 1),
('Juan Camilo Soto', 'juan.soto@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3078889901', 'Bogotá', '4.722000,-74.072500', 'https://randomuser.me/api/portraits/men/24.jpg', 'Plomería y gas domiciliario', 'https://portfolio.example.com/juan', 'Trabajos garantizados', 1),
('Liliana Navas', 'liliana.navas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3089990012', 'Bogotá', '4.704500,-74.081000', 'https://randomuser.me/api/portraits/women/25.jpg', 'Limpieza de oficinas', 'https://portfolio.example.com/liliana', 'Ambientes laborales impecables', 1),
('Mauricio Peña', 'mauricio.pena@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3090001123', 'Bogotá', '4.716500,-74.067500', 'https://randomuser.me/api/portraits/men/25.jpg', 'Instalaciones eléctricas residenciales', 'https://portfolio.example.com/mauricio', 'Iluminación y seguridad', 1),
('Diana Salazar', 'diana.salazar@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3101112234', 'Bogotá', '4.710989,-74.072090', 'https://randomuser.me/api/portraits/women/26.jpg', 'Diseño de interiores', 'https://portfolio.example.com/diana', 'Diseños personalizados', 1),
('Jhon Fredy Mora', 'jhon.mora@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3112223345', 'Bogotá', '4.712500,-74.070000', 'https://randomuser.me/api/portraits/men/26.jpg', 'Obras civiles', 'https://portfolio.example.com/jhon', 'Construcción de calidad', 1),
('Patricia Londoño', 'patricia.londono@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3123334456', 'Bogotá', '4.709000,-74.075000', 'https://randomuser.me/api/portraits/women/27.jpg', 'Limpieza detallada', 'https://portfolio.example.com/patricia', 'Detalle en cada rincón', 1),
('Santiago Rueda', 'santiago.rueda@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3134445567', 'Bogotá', '4.715000,-74.080000', 'https://randomuser.me/api/portraits/men/27.jpg', 'Instalaciones eléctricas industriales', 'https://portfolio.example.com/santiago', 'Soluciones de alta demanda', 1),
('Luisa Rey', 'luisa.rey@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3145556678', 'Bogotá', '4.705000,-74.065000', 'https://randomuser.me/api/portraits/women/28.jpg', 'Pintura y decoración', 'https://portfolio.example.com/luisa', 'Ambientes llenos de vida', 1),
('Carlos Marin', 'carlos.marin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3156667789', 'Bogotá', '4.720000,-74.060000', 'https://randomuser.me/api/portraits/men/28.jpg', 'Plomería y remodelación de baños', 'https://portfolio.example.com/carlosmarin', 'Baños modernos y funcionales', 1),
('Estefania Diaz', 'estefania.diaz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3167778890', 'Bogotá', '4.725000,-74.085000', 'https://randomuser.me/api/portraits/women/29.jpg', 'Limpieza de hogares', 'https://portfolio.example.com/estefania', 'Tu casa siempre lista', 1),
('German Beltran', 'german.beltran@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3178889901', 'Bogotá', '4.708000,-74.085000', 'https://randomuser.me/api/portraits/men/29.jpg', 'Construcción y estructuras', 'https://portfolio.example.com/german', 'Proyectos sólidos', 1),
('Melissa Parra', 'melissa.parra@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3189990012', 'Bogotá', '4.700000,-74.070000', 'https://randomuser.me/api/portraits/women/30.jpg', 'Organización de espacios', 'https://portfolio.example.com/melissa', 'Orden y armonía', 1),
('Hector Cruz', 'hector.cruz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3190001123', 'Bogotá', '4.718000,-74.078000', 'https://randomuser.me/api/portraits/men/30.jpg', 'Electricista residencial e industrial', 'https://portfolio.example.com/hector', 'Instalaciones confiables', 1),
('Daniela Prieto', 'daniela.prieto@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3201112234', 'Bogotá', '4.713000,-74.060000', 'https://randomuser.me/api/portraits/women/31.jpg', 'Pintura decorativa y artística', 'https://portfolio.example.com/danielap', 'Murales y detalles únicos', 1),
('Andres Beltran', 'andres.beltran@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3212223345', 'Bogotá', '4.706000,-74.078500', 'https://randomuser.me/api/portraits/men/31.jpg', 'Plomería de emergencia', 'https://portfolio.example.com/andresb', 'Atención 24/7', 1),
('Yolanda Suarez', 'yolanda.suarez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3223334456', 'Bogotá', '4.722000,-74.072500', 'https://randomuser.me/api/portraits/women/32.jpg', 'Limpieza de fin de obra', 'https://portfolio.example.com/yolanda', 'Entregas impecables', 1),
('Camilo Guerra', 'camilo.guerra@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3234445567', 'Bogotá', '4.704500,-74.081000', 'https://randomuser.me/api/portraits/men/32.jpg', 'Remodelación integral de hogares', 'https://portfolio.example.com/camilog', 'Transformaciones completas', 1),
('Veronica Cortes', 'veronica.cortes@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3245556679', 'Bogotá', '4.699500,-74.075500', 'https://randomuser.me/api/portraits/women/33.jpg', '8 años en limpieza de oficinas', 'https://portfolio.example.com/veronica', 'Orden y pulcritud para empresas', 1),
('Bernardo Acero', 'bernardo.acero@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3256667780', 'Bogotá', '4.727500,-74.068500', 'https://randomuser.me/api/portraits/men/33.jpg', 'Especialista en estructuras metálicas', 'https://portfolio.example.com/bernardo', 'Refuerzos y ampliaciones seguras', 1),
('Gloria Medina', 'gloria.medina@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3267778891', 'Bogotá', '4.701200,-74.082300', 'https://randomuser.me/api/portraits/women/34.jpg', 'Pintura y acabados finos', 'https://portfolio.example.com/gloria', 'Detalles de alta gama', 1),
('Rafael Quintero', 'rafael.quintero@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3278889902', 'Bogotá', '4.729000,-74.073500', 'https://randomuser.me/api/portraits/men/34.jpg', 'Electricidad en altura', 'https://portfolio.example.com/rafael', 'Mantenimiento para edificios', 1),
('Silvia Torres', 'silvia.torres@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3289990013', 'Bogotá', '4.703300,-74.063800', 'https://randomuser.me/api/portraits/women/35.jpg', 'Diseño y montaje de cocinas', 'https://portfolio.example.com/silvia', 'Cocinas funcionales y estéticas', 1),
('Hugo Cabrera', 'hugo.cabrera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3290001124', 'Bogotá', '4.718800,-74.084200', 'https://randomuser.me/api/portraits/men/35.jpg', 'Plomería industrial', 'https://portfolio.example.com/hugoc', 'Soluciones para grandes proyectos', 1);

-- 5. Ubicaciones (puntos exactos dentro de Bogotá para el mapa)
INSERT INTO UBICACION (ciudad, departamento, direccion, latitud, longitud) VALUES
('Bogotá', 'Cundinamarca', 'Punto 1', 4.710989, -74.072090),
('Bogotá', 'Cundinamarca', 'Punto 2', 4.712500, -74.070000),
('Bogotá', 'Cundinamarca', 'Punto 3', 4.709000, -74.075000),
('Bogotá', 'Cundinamarca', 'Punto 4', 4.715000, -74.080000),
('Bogotá', 'Cundinamarca', 'Punto 5', 4.705000, -74.065000),
('Bogotá', 'Cundinamarca', 'Punto 6', 4.720000, -74.060000),
('Bogotá', 'Cundinamarca', 'Punto 7', 4.725000, -74.085000),
('Bogotá', 'Cundinamarca', 'Punto 8', 4.708000, -74.085000),
('Bogotá', 'Cundinamarca', 'Punto 9', 4.700000, -74.070000),
('Bogotá', 'Cundinamarca', 'Punto 10', 4.718000, -74.078000),
('Bogotá', 'Cundinamarca', 'Punto 11', 4.713000, -74.060000),
('Bogotá', 'Cundinamarca', 'Punto 12', 4.706000, -74.078500),
('Bogotá', 'Cundinamarca', 'Punto 13', 4.722000, -74.072500),
('Bogotá', 'Cundinamarca', 'Punto 14', 4.704500, -74.081000),
('Bogotá', 'Cundinamarca', 'Punto 15', 4.716500, -74.067500);

-- 6. Relación Contratista - Servicio
-- Pedro (Plomero) -> Reparación de Tuberías
INSERT INTO CONTRATISTA_SERVICIO (id_contratista, id_servicio, precio_personalizado, descripcion_personalizada) VALUES
(1, 2, 85000.00, 'Incluye materiales básicos');

-- Ana (Pintora) -> Pintura de Interiores
INSERT INTO CONTRATISTA_SERVICIO (id_contratista, id_servicio, precio_personalizado, descripcion_personalizada) VALUES
(2, 4, 28000.00, 'Precio por metro cuadrado');

-- Luis (Electricista) -> Instalación Eléctrica
INSERT INTO CONTRATISTA_SERVICIO (id_contratista, id_servicio, precio_personalizado, descripcion_personalizada) VALUES
(3, 3, 130000.00, 'Revisión inicial gratuita');

-- Sofia (Limpieza) -> Limpieza General
INSERT INTO CONTRATISTA_SERVICIO (id_contratista, id_servicio, precio_personalizado, descripcion_personalizada) VALUES
(4, 1, 55000.00, 'Turno de 4 horas');

-- Jorge (Constructor) -> Remodelación de Baños
INSERT INTO CONTRATISTA_SERVICIO (id_contratista, id_servicio, precio_personalizado, descripcion_personalizada) VALUES
(5, 5, 1600000.00, 'Mano de obra completa');

-- 7. Relación Contratista - Ubicación
INSERT INTO CONTRATISTA_UBICACION (id_contratista, id_ubicacion) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 1),
(17, 2),
(18, 3),
(19, 4),
(20, 5),
(21, 6),
(22, 7),
(23, 8),
(24, 9),
(25, 10),
(26, 11),
(27, 12),
(28, 13),
(29, 14),
(30, 15),
(31, 1),
(32, 2),
(33, 3),
(34, 4),
(35, 5);
