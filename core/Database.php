<?php

class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            $config = require __DIR__ . '/../.env.php';

            self::$pdo = new PDO(
                "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4",
                $config['DB_USER'],
                $config['DB_PASS'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$pdo;
    }
}
