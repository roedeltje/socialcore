<?php
/**
 * 📄 Voorbeeld databaseconfiguratie
 *
 * Kopieer dit bestand naar `config/database.php` en vul je eigen gegevens in.
 * Dit bestand wordt genegeerd door Git om gevoelige info te beschermen.
 */

return [
    'host'     => 'localhost', // of je database host
    'database' => 'jouw_database_naam',
    'username' => 'jouw_database_gebruiker',
    'password' => 'jouw_database_wachtwoord',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
