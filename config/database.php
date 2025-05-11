<?php
// /config/database.php

return [
    'host'     => 'localhost',
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