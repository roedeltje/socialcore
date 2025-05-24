<?php
namespace App\Database;

class Database
{
    private static $instance = null;
    private $pdo;
    
    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        
        $this->pdo = new \PDO($dsn, $config['username'], $config['password'], $config['options']);
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getPdo()
    {
        return $this->pdo;
    }
    
    public function query($query, $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($query, $params = [])
{
    $stmt = $this->query($query, $params);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $result === false ? false : $result;
}
    
    public function fetchAll($query, $params = [])
    {
        return $this->query($query, $params)->fetchAll();
    }

    // In app/Database/Database.php (nieuwe methode)

public function ensurePostMediaFields() {
    // Controleer of 'type' kolom bestaat in posts tabel
    $checkTypeColumn = $this->pdo->query("SHOW COLUMNS FROM `posts` LIKE 'type'");
    if ($checkTypeColumn->rowCount() == 0) {
        // Voeg type kolom toe als deze niet bestaat
        $this->pdo->query("ALTER TABLE `posts` ADD COLUMN `type` VARCHAR(20) DEFAULT 'text' AFTER `content`");
    }
    
    // Controleer of post_media tabel bestaat
    $checkMediaTable = $this->pdo->query("SHOW TABLES LIKE 'post_media'");
    if ($checkMediaTable->rowCount() == 0) {
        // Maak post_media tabel aan
        $createMediaTable = "CREATE TABLE `post_media` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `post_id` INT NOT NULL,
            `file_path` VARCHAR(255) NOT NULL,
            `file_type` VARCHAR(50) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE
        )";
        $this->pdo->query($createMediaTable);
    }
    
    return true;
}
}