<?php
// app/Controllers/HomeController.php

class HomeController extends Controller
{
    public function index()
    {
        // Voeg hier eventuele logica toe voor de homepage
        // Misschien wil je recente posts ophalen of statistieken tonen
        
        // Gebruik de view methode van de basiscontroller
        $this->view('home');
        
        // Of als je nog geen basiscontroller hebt:
        /*
        include __DIR__ . '/../../core/views/layout/header.php';
        include __DIR__ . '/../../core/views/home.php';
        include __DIR__ . '/../../core/views/layout/footer.php';
        */
    }
}