<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Models\Database;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Database::getInstance();

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

foreach ($files as $file) {
    $query = file_get_contents($file);
    try {
        $pdo->exec($query);
        echo "Executed migration: " . basename($file) . PHP_EOL;
    } catch (PDOException $e) {
        echo "Error executing migration " . basename($file) . ": " . $e->getMessage() . PHP_EOL;
    }
}