<?php

namespace App\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Je kunt hier admin-specifieke gegevens ophalen
        $this->view('admin/dashboard');
    }
}