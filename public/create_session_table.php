<?php

// Load CodeIgniter's bootstrap file to get access to the framework
require __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Load the database
$db = \Config\Database::connect();

$forge = \Config\Database::forge();

echo "Creating session table...\n";

$fields = [
    'id' => [
        'type' => 'VARCHAR',
        'constraint' => 128,
        'null' => false,
    ],
    'ip_address' => [
        'type' => 'VARCHAR',
        'constraint' => 45,
        'null' => false,
    ],
    'timestamp' => [
        'type' => 'INT',
        'unsigned' => true,
        'default' => 0,
        'null' => false,
    ],
    'data' => [
        'type' => 'BLOB',
        'null' => false,
    ],
];

$forge->addField($fields);
$forge->addKey('id', true);
$forge->addKey('timestamp');
$forge->createTable('ci_sessions', true); // true = IF NOT EXISTS

echo "Session table created successfully.\n";
