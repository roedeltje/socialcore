<?php
namespace App\Controllers;

use App\Auth\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Je kunt hier data toevoegen voor de view
        $data = [
            'title' => 'Welkom bij SocialCore',
            // Extra data indien nodig
        ];
        
        $this->view('home/index', $data);
    }
}