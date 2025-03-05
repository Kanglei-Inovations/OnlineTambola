<?php
header('Content-Type: application/json');

// Include database connection
require 'db_connection.php';

// Check if game_id is provided
if (isset($_GET['game_id']) && is_numeric($_GET['game_id'])) {
    $game_id = (int)$_GET['game_id'];

    // Get count of called numbers for the game
    $result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM called_numbers WHERE game_id = $game_id");
    $data = mysqli_fetch_assoc($result);
    $calledNumbers = (int)$data['count'];

    // Calculate progress (out of 99 numbers)
    $totalNumbers = 99;
    $progress = min(100, round(($calledNumbers / $totalNumbers) * 100));

    echo json_encode(['success' => true, 'progress' => $progress]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}

// Close database connection
mysqli_close($conn);
