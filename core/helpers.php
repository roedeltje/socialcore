<?php
 /**
 * Genereer een absolute URL
 *
 * @param string $path Optioneel pad om toe te voegen aan de basis URL
 * @return string Volledige URL
 */

function base_url(string $path = ''): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host;
    
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Redirect naar een andere URL
 *
 * @param string $path Pad om naartoe te redirecten
 * @return void
 */

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/**
 * Controleert of een gebruiker is ingelogd
 * 
 * @return bool True als gebruiker is ingelogd, anders False
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}