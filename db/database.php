<?php
$host = 'localhost';
$dbname = 'loginDemo';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // create the table if it does not exists
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        api_token VARCHAR(255) UNIQUE NULL
    )";
    $db->exec($query);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
