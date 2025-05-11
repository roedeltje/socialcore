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
        return $this->query($query, $params)->fetch();
    }
    
    public function fetchAll($query, $params = [])
    {
        return $this->query($query, $params)->fetchAll();
    }
}