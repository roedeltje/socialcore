<?php
// app/Controllers/DashboardController.php

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        $user = Auth::user();
        
        // Gebruik de view methode van de basiscontroller
        $this->view('dashboard/index', ['user' => $user]);
    }
}