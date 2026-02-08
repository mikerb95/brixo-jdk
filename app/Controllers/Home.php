<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string|RedirectResponse
    {
        helper('form');

        $session = session();
        $data = [
            'user' => $session->get('user'),
            'message' => $session->getFlashdata('message'),
            'error' => $session->getFlashdata('error'),
            'login_error' => $session->getFlashdata('login_error'),
            // keep minimal placeholders for compatibility with the view
            'register_error' => $session->getFlashdata('register_error'),
            'register_old' => $session->getFlashdata('register_old') ?? [],
            'userContracts' => [],
            'contractorContracts' => [],
        ];

        // For educational purposes the detailed login/register logic
        // was moved to `Auth` controller. Home now only renders the view.

        return view('index', $data);
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/')->with('message', 'Sesión cerrada.');
    }
}