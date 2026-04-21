<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Updated configuration to use local MariaDB on port 3307
$host = '127.0.0.1';
$port = '3307';
$dbname = 'campusbook';
$username = 'campusbook_user';
$password = 'campusbook_pass';

try {
    // Added port to the DSN
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . ". Please ensure the local database server is running on port 3307.");
}
