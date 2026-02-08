<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = true;
    protected $migrateOnce = true;
    protected $refresh = false;
    protected $seed = [];
    protected $baseURI = 'http://localhost:8080';

    // For this test, we might want to migrate/seed if the DB is empty, 
    // but since we are testing against a running app, we assume the schema exists.
    // DatabaseTestTrait will wrap each test in a transaction and rollback.

    public function testRegisterClient()
    {
        $db = \Config\Database::connect();
        $this->assertNotEmpty($db);

        $email = 'client_' . time() . '@test.com';

        $result = $this->call('post', '/register', [
            'action' => 'register',
            'nombre' => 'Test Client',
            'correo' => $email,
            'telefono' => '1234567890',
            'contrasena' => 'password123',
            'contrasena_confirm' => 'password123',
            'rol' => 'cliente'
        ]);

        $result->assertRedirectTo('/');
        // $result->assertSessionHas('message', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');

        // $this->seeInDatabase('CLIENTE', [
        //     'correo' => $email,
        //     'nombre' => 'Test Client'
        // ]);
    }

    public function testRegisterContractor()
    {
        $email = 'contractor_' . time() . '@test.com';

        $result = $this->call('post', '/register', [
            'action' => 'register',
            'nombre' => 'Test Contractor',
            'correo' => $email,
            'telefono' => '0987654321',
            'contrasena' => 'password123',
            'contrasena_confirm' => 'password123',
            'rol' => 'contratista',
            'ciudad' => 'Bogotá',
            'ubicacion_mapa' => '4.710989,-74.072090',
        ]);

        $result->assertRedirectTo('/');
        // $result->assertSessionHas('message', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');

        // $this->seeInDatabase('CONTRATISTA', [
        //     'correo' => $email,
        //     'nombre' => 'Test Contractor'
        // ]);
    }

    /*
    public function testLoginClient()
    {
        $email = 'loginclient_' . time() . '@test.com';

        // First register
        $this->call('post', '/register', [
            'action' => 'register',
            'nombre' => 'Login Client',
            'correo' => $email,
            'telefono' => '111222333',
            'contrasena' => 'password123',
            'contrasena_confirm' => 'password123',
            'rol' => 'cliente'
        ]);

        // Then login
        $result = $this->call('post', '/login', [
            'correo' => $email,
            'contrasena' => 'password123'
        ]);

        $result->assertRedirectTo('/');
        $result->assertSessionHas('message', 'Inicio de sesión correcto. ¡Bienvenido!');

        $user = session('user');
        $this->assertEquals('cliente', $user['rol']);
        $this->assertEquals($email, $user['correo']);
    }
    */

    /*
    public function testLoginContractor()
    {
        $email = 'logincontractor_' . time() . '@test.com';

        // First register
        $this->call('post', '/register', [
            'action' => 'register',
            'nombre' => 'Login Contractor',
            'correo' => $email,
            'telefono' => '444555666',
            'contrasena' => 'password123',
            'contrasena_confirm' => 'password123',
            'rol' => 'contratista',
            'ciudad' => 'Bogotá',
            'ubicacion_mapa' => '4.710989,-74.072090',
        ]);

        // Then login
        $result = $this->call('post', '/login', [
            'action' => 'login',
            'correo' => $email,
            'contrasena' => 'password123'
        ]);

        $result->assertRedirectTo('/');
        $result->assertSessionHas('message', 'Inicio de sesión correcto. ¡Bienvenido!');

        $user = session('user');
        $this->assertEquals('contratista', $user['rol']);
    }
    */

    /*
    public function testLoginInvalid()
    {
        $result = $this->call('post', '/login', [
            'action' => 'login',
            'correo' => 'nonexistent@test.com',
            'contrasena' => 'wrongpassword'
        ]);

        $result->assertRedirectTo('/');
        $result->assertSessionHas('login_error', 'No encontramos una cuenta asociada a ese correo.');
    }
    */
}
