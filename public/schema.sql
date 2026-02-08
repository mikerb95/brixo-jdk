-- Schema compatible con la aplicación actual (Legacy + Features)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS RESENA;
DROP TABLE IF EXISTS CONTRATO;
DROP TABLE IF EXISTS CONTRATISTA_SERVICIO;
DROP TABLE IF EXISTS CONTRATISTA_UBICACION;
DROP TABLE IF EXISTS SERVICIO;
DROP TABLE IF EXISTS CATEGORIA;
DROP TABLE IF EXISTS UBICACION;
DROP TABLE IF EXISTS CONTRATISTA;
DROP TABLE IF EXISTS CLIENTE;
DROP TABLE IF EXISTS USUARIO; -- Limpiar tabla del intento anterior
DROP TABLE IF EXISTS COTIZACION;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tabla CLIENTE
CREATE TABLE CLIENTE (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  telefono VARCHAR(50),
  ciudad VARCHAR(100),
  foto_perfil VARCHAR(255),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla CONTRATISTA
CREATE TABLE CONTRATISTA (
  id_contratista INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  telefono VARCHAR(50),
  ciudad VARCHAR(100),
  ubicacion_mapa VARCHAR(255),
  foto_perfil VARCHAR(255),
  experiencia TEXT,
  portafolio TEXT,
  descripcion_perfil TEXT,
  verificado TINYINT(1) DEFAULT 0,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla CATEGORIA
CREATE TABLE CATEGORIA (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  imagen_url VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla SERVICIO
CREATE TABLE SERVICIO (
  id_servicio INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  precio_estimado DECIMAL(12,2),
  imagen_url VARCHAR(255),
  id_categoria INT,
  CONSTRAINT fk_servicio_categoria FOREIGN KEY (id_categoria) REFERENCES CATEGORIA(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla UBICACION
CREATE TABLE UBICACION (
  id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
  ciudad VARCHAR(100),
  departamento VARCHAR(100),
  direccion VARCHAR(255),
  latitud DECIMAL(10, 8),
  longitud DECIMAL(11, 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabla Intermedia CONTRATISTA_SERVICIO
CREATE TABLE CONTRATISTA_SERVICIO (
  id_contratista INT,
  id_servicio INT,
  precio_personalizado DECIMAL(12,2),
  descripcion_personalizada TEXT,
  PRIMARY KEY (id_contratista, id_servicio),
  CONSTRAINT fk_cs_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE,
  CONSTRAINT fk_cs_servicio FOREIGN KEY (id_servicio) REFERENCES SERVICIO(id_servicio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabla Intermedia CONTRATISTA_UBICACION
CREATE TABLE CONTRATISTA_UBICACION (
  id_contratista INT,
  id_ubicacion INT,
  PRIMARY KEY (id_contratista, id_ubicacion),
  CONSTRAINT fk_cu_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE,
  CONSTRAINT fk_cu_ubicacion FOREIGN KEY (id_ubicacion) REFERENCES UBICACION(id_ubicacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabla CONTRATO
CREATE TABLE CONTRATO (
  id_contrato INT AUTO_INCREMENT PRIMARY KEY,
  fecha_inicio DATE,
  fecha_fin DATE,
  costo_total DECIMAL(12,2),
  estado ENUM('PENDIENTE','ACTIVO','COMPLETADO','CANCELADO') DEFAULT 'PENDIENTE',
  id_contratista INT NOT NULL,
  id_cliente INT NOT NULL,
  CONSTRAINT fk_contrato_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista),
  CONSTRAINT fk_contrato_cliente FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabla RESENA
CREATE TABLE RESENA (
  id_resena INT AUTO_INCREMENT PRIMARY KEY,
  comentario TEXT,
  fecha DATE,
  calificacion TINYINT UNSIGNED NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
  id_contrato INT NOT NULL,
  id_cliente INT NOT NULL,
  CONSTRAINT fk_resena_contrato FOREIGN KEY (id_contrato) REFERENCES CONTRATO(id_contrato),
  CONSTRAINT fk_resena_cliente FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabla CERTIFICACION
CREATE TABLE CERTIFICACION (
  id_certificado INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  entidad_emisora VARCHAR(255),
  fecha_obtenida DATE,
  id_contratista INT NOT NULL,
  CONSTRAINT fk_cert_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;