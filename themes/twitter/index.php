<?php
/**
 * SocialCore Default Theme
 *
 * Dit is het hoofdbestand van het standaardthema voor SocialCore.
 * Dit bestand dient voornamelijk voor veiligheidsdoeleinden en documentatie.
 * De werkelijke themabestanden bevinden zich in de subdirectories:
 * - layouts/ (header, footer, etc.)
 * - pages/ (home, profile, etc.)
 * - components/ (herbruikbare onderdelen)
 * - templates/ (specifieke templates)
 * - assets/ (css, js, images)
 */

// Voorkom directe toegang
if (!defined('SOCIALCORE')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Directe toegang tot dit bestand is niet toegestaan.');
}