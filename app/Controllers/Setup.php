<?php

namespace App\Controllers;

class Setup extends BaseController
{
    public function solicitudes()
    {
        $db = db_connect();

        $sql = "
        CREATE TABLE IF NOT EXISTS SOLICITUD (
            id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
            id_cliente INT NOT NULL,
            id_contratista INT NULL, -- NULL significa que es una solicitud abierta para todos
            titulo VARCHAR(150) NOT NULL,
            descripcion TEXT NOT NULL,
            presupuesto DECIMAL(12,2) DEFAULT 0,
            ubicacion VARCHAR(255),
            estado ENUM('ABIERTA', 'ASIGNADA', 'COMPLETADA', 'CANCELADA') DEFAULT 'ABIERTA',
            creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_solicitud_cliente FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente) ON DELETE CASCADE,
            CONSTRAINT fk_solicitud_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $db->query($sql);
            echo "<h1>✅ Tabla SOLICITUD creada correctamente.</h1>";
            echo "<p>La base de datos se ha actualizado usando la conexión nativa de CodeIgniter.</p>";
            echo "<p><a href='/panel'>Volver al Panel</a></p>";
        } catch (\Throwable $e) {
            echo "<h1>❌ Error al crear la tabla:</h1>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function update_cliente()
    {
        $db = db_connect();
        try {
            // Check if column exists
            $fields = $db->getFieldData('CLIENTE');
            $exists = false;
            foreach ($fields as $field) {
                if ($field->name === 'ciudad') {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $db->query("ALTER TABLE CLIENTE ADD COLUMN ciudad VARCHAR(100) AFTER telefono");
                echo "<h1>✅ Columna 'ciudad' agregada a la tabla CLIENTE.</h1>";
            } else {
                echo "<h1>ℹ️ La columna 'ciudad' ya existe en la tabla CLIENTE.</h1>";
            }
            echo "<p><a href='/'>Volver al Inicio</a></p>";
        } catch (\Throwable $e) {
            echo "<h1>❌ Error:</h1>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function update_fotos()
    {
        $db = db_connect();
        try {
            // Check CLIENTE
            $fields = $db->getFieldData('CLIENTE');
            $exists = false;
            foreach ($fields as $field) {
                if ($field->name === 'foto_perfil') {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE CLIENTE ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL");
                echo "<h1>✅ Columna 'foto_perfil' agregada a CLIENTE.</h1>";
            } else {
                echo "<h1>ℹ️ Columna 'foto_perfil' ya existe en CLIENTE.</h1>";
            }

            // Check CONTRATISTA
            $fields = $db->getFieldData('CONTRATISTA');
            $exists = false;
            foreach ($fields as $field) {
                if ($field->name === 'foto_perfil') {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $db->query("ALTER TABLE CONTRATISTA ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL");
                echo "<h1>✅ Columna 'foto_perfil' agregada a CONTRATISTA.</h1>";
            } else {
                echo "<h1>ℹ️ Columna 'foto_perfil' ya existe en CONTRATISTA.</h1>";
            }

            echo "<p><a href='/panel'>Volver al Panel</a></p>";

        } catch (\Throwable $e) {
            echo "<h1>❌ Error:</h1>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function mensajes()
    {
        $db = db_connect();

        $sql = "
        CREATE TABLE IF NOT EXISTS MENSAJE (
            id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
            remitente_id INT NOT NULL,
            remitente_rol ENUM('cliente', 'contratista') NOT NULL,
            destinatario_id INT NOT NULL,
            destinatario_rol ENUM('cliente', 'contratista') NOT NULL,
            contenido TEXT NOT NULL,
            leido TINYINT(1) DEFAULT 0,
            creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $db->query($sql);
            echo "<h1>✅ Tabla MENSAJE creada correctamente.</h1>";
            echo "<p>Sistema de mensajería listo para usar.</p>";
            echo "<p><a href='/mensajes'>Ir a Mensajes</a></p>";
        } catch (\Throwable $e) {
            echo "<h1>❌ Error al crear la tabla:</h1>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}
