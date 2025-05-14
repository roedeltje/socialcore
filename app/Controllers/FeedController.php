<?php

namespace App\Controllers;

class FeedController
{
    /**
     * Toon de hoofdpagina van de nieuwsfeed
     */
    public function index()
    {
        // Hier zou je later data kunnen ophalen, zoals posts van gebruikers
        // Voor nu gebruiken we gewoon dummy data in de view
        
        view('feed/index');
    }
    
    /**
     * Een methode voor het later toevoegen van nieuwe posts
     */
    public function create()
    {
        // Functionaliteit voor het maken van nieuwe posts
        // Komt in een latere fase
    }
    
    /**
     * Een methode voor het ophalen van meer posts (bijv. voor oneindige scroll)
     */
    public function loadMore()
    {
        // Functionaliteit voor het laden van meer posts
        // Komt in een latere fase
    }
}