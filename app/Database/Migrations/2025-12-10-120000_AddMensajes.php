<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMensajes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mensaje' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'remitente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'remitente_rol' => [
                'type' => 'ENUM',
                'constraint' => ['cliente', 'contratista'],
            ],
            'destinatario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'destinatario_rol' => [
                'type' => 'ENUM',
                'constraint' => ['cliente', 'contratista'],
            ],
            'contenido' => [
                'type' => 'TEXT',
            ],
            'leido' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'creado_en' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null, // Will be handled by model or DB default
            ],
        ]);
        $this->forge->addKey('id_mensaje', true);
        $this->forge->createTable('MENSAJE');

        // Add default timestamp via raw SQL because Forge sometimes struggles with defaults
        $this->db->query("ALTER TABLE MENSAJE MODIFY creado_en DATETIME DEFAULT CURRENT_TIMESTAMP");
    }

    public function down()
    {
        $this->forge->dropTable('MENSAJE');
    }
}
