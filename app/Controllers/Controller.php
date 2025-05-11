<?php
// app/Controllers/Controller.php

class Controller
{
    protected function view($view, $data = [])
    {
        // Maak variabelen beschikbaar in de view
        extract($data);
        
        // Laad header, view en footer
        include __DIR__ . '/../../core/views/layout/header.php';
        include __DIR__ . '/../../core/views/' . $view . '.php';
        include __DIR__ . '/../../core/views/layout/footer.php';
    }
}