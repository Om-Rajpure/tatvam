<?php

echo "<pre>";

echo "DB_HOST = " . getenv('DB_HOST') . PHP_EOL;
echo "DB_PORT = " . getenv('DB_PORT') . PHP_EOL;
echo "DB_NAME = " . getenv('DB_NAME') . PHP_EOL;
echo "DB_USER = " . getenv('DB_USER') . PHP_EOL;

try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') .
        ";port=" . getenv('DB_PORT') .
        ";dbname=" . getenv('DB_NAME'),
        getenv('DB_USER'),
        getenv('DB_PASSWORD')
    );

    echo "\n✅ Connected Successfully";
} catch (PDOException $e) {
    echo "\n❌ " . $e->getMessage();
}