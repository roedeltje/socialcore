<?php

namespace App\Controllers;

class AboutController extends Controller
{
    /**
     * Toon de Over-pagina
     */
    public function index()
    {
        $data = [
            'title' => 'Over SocialCore',
            'active_menu' => 'about'
        ];
        
        $this->view('about/index', $data);
    }
}