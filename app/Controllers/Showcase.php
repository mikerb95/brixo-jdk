<?php

namespace App\Controllers;

class Showcase extends BaseController
{
    public function index(): string
    {
        return view('showcase');
    }
}
