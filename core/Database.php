<?php
class Database {
    private static $pdo;

    public static function connect() {
        $envPath = __DIR__ . '/../.env.php';
        $exampleEnvPath = __DIR__ . '/../.env.example.php';
        
        if (!file_exists($envPath)) {
            $errorMsg = '<h1>Database configuratie ontbreekt</h1>';
            $errorMsg .= '<p>Het bestand \'.env.php\' is niet gevonden. Volg deze stappen om het probleem op te lossen:</p>';
            $errorMsg .= '<ol>';
            $errorMsg .= '<li>Maak een kopie van \'.env.example.php\' en noem het \'.env.php\'</li>';
            $errorMsg .= '<li>Open het nieuwe \'.env.php\' bestand en vul de juiste database-instellingen in</li>';
            $errorMsg .= '<li>Zorg ervoor dat de database bestaat en toegankelijk is</li>';
            $errorMsg .= '</ol>';
            
            if (!file_exists($exampleEnvPath)) {
                $errorMsg .= '<p><strong>Let op:</strong> Het voorbeeldbestand \'.env.example.php\' is ook niet gevonden. Download het project opnieuw of maak handmatig een configuratiebestand aan.</p>';
            }
            
            die($errorMsg);
        }
        
        if (!self::$pdo) {
            try {
                $config = require $envPath;
                
                // Controleer of alle benodigde configuratiewaarden aanwezig zijn
                $requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
                foreach ($requiredKeys as $key) {
                    if (!isset($config[$key])) {
                        die("<h1>Configuratiefout</h1><p>De vereiste configuratiewaarde '{$key}' ontbreekt in je .env.php bestand.</p>");
                    }
                }

                self::$pdo = new PDO(
                    "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4",
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("<h1>Database verbindingsfout</h1><p>Er kon geen verbinding worden gemaakt met de database. Controleer je instellingen in .env.php.</p><p><strong>Foutmelding:</strong> " . $e->getMessage() . "</p>");
            }
        }

        return self::$pdo;
    }
}
