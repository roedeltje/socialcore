<?php

class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                'mysql:host=localhost;dbname=socialcore_db;charset=utf8mb4',
                'socialcore_us',
                '2Ai85#ts6Hldtr8',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$pdo;
    }
}
