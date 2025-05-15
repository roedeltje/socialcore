<?php
// /config/app.php

return [
    // Algemene applicatie instellingen
    'name' => 'SocialCore',
    'version' => '1.0.0',
    
    // Omgeving: 'development', 'staging', of 'production'
    'environment' => 'development',
    
    // Tijdzone
    'timezone' => 'Europe/Amsterdam',
    
    // Debugging instellingen
    'debug' => true,
    
    // Base URL (optioneel, als je dit niet al in helpers hebt)
    'url' => 'https://dev.socialcoreproject.nl',
    
    // Sessie instellingen
    'session' => [
        'lifetime' => 120, // minuten
        'secure' => false, // alleen https
        'http_only' => true,
    ],
    
    // Upload instellingen
    'uploads' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_images' => ['image/jpeg', 'image/png', 'image/gif'],
        'allowed_attachments' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    ],
];