<?php
/**
 * API Routes voor SocialCore
 * 
 * Dit bestand definieert alle API routes voor het SocialCore platform.
 * API routes beginnen allemaal met /api/v1/
 */

return [
    // Test endpoint
    'ping' => function() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'pong',
            'time' => date('Y-m-d H:i:s')
        ]);
    },
    
    // Gebruikers endpoints
    'users' => function() {
        header('Content-Type: application/json');
        // Hier zou je normaal gesproken data uit een database halen
        echo json_encode([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'name' => 'Test Gebruiker'],
                ['id' => 2, 'name' => 'Nog Een Gebruiker']
            ]
        ]);
    },
    
    // Individuele gebruiker ophalen (voorbeeld voor dynamische routes)
    'users/1' => function() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => 1,
                'name' => 'Test Gebruiker',
                'email' => 'test@example.com',
                'created_at' => '2023-01-01'
            ]
        ]);
    }
];