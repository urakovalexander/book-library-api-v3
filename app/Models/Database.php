<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        try {
            $this->connection = new PDO(
                "{$config['driver']}:host={$config['host']};dbname={$config['database']}",
                $config['username'],
                $config['password']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("SET NAMES '{$config['charset']}' COLLATE '{$config['collation']}'");
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO
    {
        if (!self::$instance) {
            self::$instance = (new Database())->connection;
        }
        return self::$instance;
    }
}