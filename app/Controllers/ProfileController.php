<?php

namespace App\Controllers;

class ProfileController extends Controller
{
    /**
     * Toon de profielpagina
     */
    public function index()
    {
        // Later kun je hier de gebruikersgegevens ophalen op basis van ID
        // Voor nu houden we het simpel
        $this->view('profile/index');
    }
}