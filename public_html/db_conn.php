<?php 
/**
 * Database Connection Handler
 * Supports Environment Variables for Render / Production Deployments
 * with safe Fallback Values for Local Development (XAMPP / WAMP / Laragon).
 */

$sName   = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$port    = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$uName   = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$pass    = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$db_name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'tatvam';

try {
    $conn = new PDO("mysql:host=$sName;port=$port;dbname=$db_name;charset=utf8mb4", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Return connection error
    echo "Connection failed: " . $e->getMessage();
}
?>