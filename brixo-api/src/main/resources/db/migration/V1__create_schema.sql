-- ═══════════════════════════════════════════════════════════════════
-- Brixo — V1: Schema completo (consolidado desde legacy PHP)
-- Incluye: tablas del schema.sql original + tablas dinámicas (Setup)
-- ═══════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ──────────────────────────────────────
-- 1. Tabla CLIENTE
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CLIENTE (
    id_cliente    INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(255)  NOT NULL,
    correo        VARCHAR(255)  NOT NULL UNIQUE,
    contrasena    VARCHAR(255)  NOT NULL,
    telefono      VARCHAR(50),
    ciudad        VARCHAR(100),
    foto_perfil   VARCHAR(255),
    creado_en     DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 2. Tabla CONTRATISTA
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CONTRATISTA (
    id_contratista    INT AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(255)  NOT NULL,
    correo            VARCHAR(255)  NOT NULL UNIQUE,
    contrasena        VARCHAR(255)  NOT NULL,
    telefono          VARCHAR(50),
    ciudad            VARCHAR(100),
    ubicacion_mapa    VARCHAR(255),
    foto_perfil       VARCHAR(255),
    experiencia       TEXT,
    portafolio        TEXT,
    descripcion_perfil TEXT,
    verificado        TINYINT(1)    DEFAULT 0,
    creado_en         DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 3. Tabla ADMIN
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS ADMIN (
    id_admin       INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255)  NOT NULL,
    correo         VARCHAR(255)  NOT NULL UNIQUE,
    contrasena     VARCHAR(255)  NOT NULL,
    foto_perfil    VARCHAR(255),
    activo         TINYINT(1)    DEFAULT 1,
    ultimo_acceso  DATETIME,
    creado_en      DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 4. Tabla CATEGORIA
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CATEGORIA (
    id_categoria  INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100)  NOT NULL,
    descripcion   TEXT,
    imagen_url    VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 5. Tabla SERVICIO
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS SERVICIO (
    id_servicio      INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(255)   NOT NULL,
    descripcion      TEXT,
    precio_estimado  DECIMAL(12,2),
    imagen_url       VARCHAR(255),
    id_categoria     INT,
    CONSTRAINT fk_servicio_categoria
        FOREIGN KEY (id_categoria) REFERENCES CATEGORIA(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 6. Tabla UBICACION
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS UBICACION (
    id_ubicacion   INT AUTO_INCREMENT PRIMARY KEY,
    ciudad         VARCHAR(100),
    departamento   VARCHAR(100),
    direccion      VARCHAR(255),
    latitud        DECIMAL(10, 8),
    longitud       DECIMAL(11, 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 7. Tabla Intermedia CONTRATISTA_SERVICIO
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CONTRATISTA_SERVICIO (
    id_contratista           INT,
    id_servicio              INT,
    precio_personalizado     DECIMAL(12,2),
    descripcion_personalizada TEXT,
    PRIMARY KEY (id_contratista, id_servicio),
    CONSTRAINT fk_cs_contratista
        FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE,
    CONSTRAINT fk_cs_servicio
        FOREIGN KEY (id_servicio) REFERENCES SERVICIO(id_servicio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 8. Tabla Intermedia CONTRATISTA_UBICACION
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CONTRATISTA_UBICACION (
    id_contratista  INT,
    id_ubicacion    INT,
    PRIMARY KEY (id_contratista, id_ubicacion),
    CONSTRAINT fk_cu_contratista
        FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE,
    CONSTRAINT fk_cu_ubicacion
        FOREIGN KEY (id_ubicacion) REFERENCES UBICACION(id_ubicacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 9. Tabla CONTRATO
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CONTRATO (
    id_contrato     INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio    DATE,
    fecha_fin       DATE,
    costo_total     DECIMAL(12,2),
    estado          ENUM('PENDIENTE','ACTIVO','COMPLETADO','CANCELADO') DEFAULT 'PENDIENTE',
    id_contratista  INT NOT NULL,
    id_cliente      INT NOT NULL,
    CONSTRAINT fk_contrato_contratista
        FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista),
    CONSTRAINT fk_contrato_cliente
        FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 10. Tabla RESENA
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS RESENA (
    id_resena     INT AUTO_INCREMENT PRIMARY KEY,
    comentario    TEXT,
    fecha         DATE,
    calificacion  TINYINT UNSIGNED NOT NULL CHECK (calificacion BETWEEN 1 AND 5),
    id_contrato   INT NOT NULL,
    id_cliente    INT NOT NULL,
    CONSTRAINT fk_resena_contrato
        FOREIGN KEY (id_contrato) REFERENCES CONTRATO(id_contrato),
    CONSTRAINT fk_resena_cliente
        FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 11. Tabla CERTIFICACION
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS CERTIFICACION (
    id_certificado   INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(255)  NOT NULL,
    entidad_emisora  VARCHAR(255),
    fecha_obtenida   DATE,
    id_contratista   INT NOT NULL,
    CONSTRAINT fk_cert_contratista
        FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 12. Tabla SOLICITUD (antes creada dinámicamente)
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS SOLICITUD (
    id_solicitud    INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente      INT NOT NULL,
    id_contratista  INT,
    titulo          VARCHAR(255) NOT NULL,
    descripcion     TEXT,
    presupuesto     DECIMAL(12,2),
    ubicacion       VARCHAR(255),
    estado          ENUM('ABIERTA','ASIGNADA','COMPLETADA','CANCELADA') DEFAULT 'ABIERTA',
    creado_en       DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitud_cliente
        FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente) ON DELETE CASCADE,
    CONSTRAINT fk_solicitud_contratista
        FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 13. Tabla MENSAJE (antes creada dinámicamente)
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS MENSAJE (
    id_mensaje        INT AUTO_INCREMENT PRIMARY KEY,
    remitente_id      INT NOT NULL,
    remitente_rol     ENUM('CLIENTE','CONTRATISTA') NOT NULL,
    destinatario_id   INT NOT NULL,
    destinatario_rol  ENUM('CLIENTE','CONTRATISTA') NOT NULL,
    contenido         TEXT NOT NULL,
    leido             TINYINT(1) DEFAULT 0,
    creado_en         DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 14. Tabla password_resets
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS password_resets (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255)  NOT NULL,
    token       VARCHAR(255)  NOT NULL,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    expires_at  DATETIME      NOT NULL,
    INDEX idx_pr_email (email),
    INDEX idx_pr_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 15. Tabla COTIZACION_CONFIRMADA
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS COTIZACION_CONFIRMADA (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente          INT,
    descripcion         TEXT,
    servicio_principal  VARCHAR(255),
    materiales_json     JSON,
    personal_json       JSON,
    complejidad         ENUM('BAJA','MEDIA','ALTA'),
    estado              ENUM('PENDIENTE','CONFIRMADA','CANCELADA') DEFAULT 'PENDIENTE',
    creado_en           DATETIME DEFAULT CURRENT_TIMESTAMP,
    confirmado_en       DATETIME,
    CONSTRAINT fk_cotiz_cliente
        FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────
-- 16. Tabla analytics_events
-- ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS analytics_events (
    id           BIGINT AUTO_INCREMENT PRIMARY KEY,
    visitor_id   VARCHAR(36),
    session_id   VARCHAR(36),
    event_type   VARCHAR(50)  NOT NULL,
    url          VARCHAR(2048),
    path         VARCHAR(512),
    referrer     VARCHAR(2048),
    title        VARCHAR(512),
    screen       VARCHAR(20),
    viewport     VARCHAR(20),
    device_type  VARCHAR(20),
    language     VARCHAR(10),
    browser      VARCHAR(50),
    platform     VARCHAR(50),
    ip_anon      VARCHAR(45),
    extra_json   JSON,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ae_visitor (visitor_id),
    INDEX idx_ae_event (event_type),
    INDEX idx_ae_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
