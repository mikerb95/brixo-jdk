<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Verifica que el usuario esté autenticado antes de permitir el acceso.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('user');

        // Si no hay usuario en sesión, redirigir al home con mensaje
        if (!$user) {
            $session->setFlashdata('login_error', 'Debes iniciar sesión para acceder a esta página.');
            return redirect()->to('/');
        }

        // Verificar roles específicos si se proporcionan argumentos
        if ($arguments !== null && !empty($arguments)) {
            $userRole = $user['rol'] ?? '';
            
            // Si el rol del usuario no está en los roles permitidos, denegar acceso
            if (!in_array($userRole, $arguments, true)) {
                $session->setFlashdata('error', 'No tienes permisos para acceder a esta página.');
                return redirect()->to('/panel');
            }
        }
    }

    /**
     * Permite filtrado después de la ejecución del controlador.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita lógica después de la respuesta
    }
}
