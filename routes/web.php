<?php
/**
 * Web Routes voor SocialCore
 * 
 * Dit bestand definieert alle web routes voor het SocialCore platform.
 */

return [
    'home' => function() {
        echo "<h1>" . __('app.welcome') . "</h1>";
        echo "<p>" . __('app.welcome_message', ['name' => 'SocialCore']) . "</p>";
        
        // Toon de taalschakelaar
        include __DIR__ . '/../app/Views/components/language_switcher.php';
    },
    
    'over' => function() {
        echo "<h1>" . __('app.about') . "</h1>";
        echo "<p>" . __('app.about_text', ['project' => 'SocialCore']) . "</p>";
    },
    
    // Deze route wordt automatisch afgehandeld in bootstrap.php
    'set-language' => function() {
        // Deze functie zou nooit rechtstreeks moeten worden aangeroepen
        // omdat de POST-request in bootstrap.php wordt afgehandeld
        echo "<h1>" . __('app.error') . "</h1>";
        echo "<p>Direct access not allowed</p>";
    }
];