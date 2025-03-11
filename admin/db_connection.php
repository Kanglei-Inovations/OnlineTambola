<?php
// Database configuration
$host = '127.0.0.1';         // Database host (usually 'localhost')
$username = 'root';          // Database username
$password = '';              // Database password
$database = 'tambola_game';  // Replace with your database name

// Create a connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die(json_encode(['error' => 'Failed to connect to database: ' . mysqli_connect_error()]));
}

// Optional: Set character set to UTF-8
mysqli_set_charset($conn, 'utf8');
