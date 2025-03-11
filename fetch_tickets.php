<?php
// header('Content-Type: application/json');

// // Database connection
// $host = '127.0.0.1';
// $user = 'root';
// $pass = '';
// $db = 'tambola_game';
// $conn = new mysqli($host, $user, $pass, $db);
// if ($conn->connect_error) {
//     echo json_encode(['error' => 'Database connection failed.']);
//     exit;
// }

// // Fetch tickets from the database
// $query = "SELECT id, player_name, ticket FROM tickets";
// $result = $conn->query($query);
// $allTickets = [];

// while ($row = $result->fetch_assoc()) {
//     $allTickets[] = $row;
// }

// echo json_encode($allTickets);
// $conn->close();

header('Content-Type: application/json');

// Database connection
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$game_id = $_GET['game_id'] ?? null;

if (!$game_id) {
    die(json_encode(['error' => 'Game ID is required']));
}

// Fetch tickets from the database
$tickets = $conn->query("SELECT * FROM tickets WHERE game_id = $game_id");

$allTickets = [];
while ($row = $tickets->fetch_assoc()) {
    $row['ticket'] = json_decode($row['ticket']); // Decode the ticket JSON
    $allTickets[] = $row;
}

echo json_encode($allTickets);
?>
