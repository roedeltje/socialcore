<?php
/**
 * SocialCore Configuratie Voorbeeld
 * 
 * Dit is een voorbeeld configuratiebestand. Om SocialCore te installeren:
 * 1. Maak een kopie van dit bestand en noem het '.env.php'
 * 2. Vul de juiste database-instellingen in
 * 3. Zorg ervoor dat het bestand niet toegankelijk is via het web
 */

return [
    // Database instellingen
    'DB_HOST' => 'localhost',     // Je database host
    'DB_NAME' => 'socialcore',    // Je database naam
    'DB_USER' => 'root',          // Je database gebruikersnaam
    'DB_PASS' => 'password',      // Je database wachtwoord

    // Applicatie instellingen
    'APP_URL' => 'http://localhost', // De URL van je applicatie
    'APP_DEBUG' => true,             // Debug modus (zet dit op false in productie)
    
    // Overige instellingen
    'TIMEZONE' => 'Europe/Amsterdam', // Tijdzone
];