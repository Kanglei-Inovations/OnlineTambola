<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Fetch tickets from the database
$query = "SELECT id, player_name, ticket FROM tickets";
$result = $conn->query($query);
$allTickets = [];

while ($row = $result->fetch_assoc()) {
    $allTickets[] = $row;
}

echo json_encode($allTickets);
$conn->close();
