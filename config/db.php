<?php
// config/db.php

// Database connection settings
$host = 'localhost';
$db_name = 'notificageral_db';
$username = 'root';
$password = 'vibbra@2021';

// PDO options for better error handling and fetch mode
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throws exceptions in case of errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetches results as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false, // Disables emulation for prepared statements (better security)
];

try {
    // Establishing the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // In case of connection failure, display the error message
    die("Error connecting to the database: " . $e->getMessage());
}

