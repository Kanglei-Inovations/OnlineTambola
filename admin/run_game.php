<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require 'db_connection.php';

date_default_timezone_set('Asia/Kolkata');  // Set timezone

echo "Connected to database.\n";

// Fetch the latest scheduled game start time directly from the 'games' table
$result = mysqli_query($conn, "SELECT id, started_at FROM games WHERE status = 'scheduled' ORDER BY id ASC LIMIT 1");
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $startTime = strtotime($row['started_at']);
    $now = time();

    echo "Current Time: " . date('H:i:s d-m-Y') . "\n";

    if ($now >= $startTime) {
        $gameId = $row['id'];
        echo "Starting game ID: $gameId\n";

        // Update game status to 'in_progress'
        mysqli_query($conn, "UPDATE games SET status = 'Game In progress...' WHERE id = $gameId");

        // Generate all 99 numbers and shuffle them
        $numbers = range(1, 99);
        shuffle($numbers);

        // Call numbers randomly every 5-10 seconds
        foreach ($numbers as $num) {
            $stmt = mysqli_prepare($conn, "INSERT INTO called_numbers (game_id, number) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $gameId, $num);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "Called number: $num\n";  // Print called number

            sleep(rand(5, 10));  // Pause for 5-10 seconds before calling the next number
        }

        // Update game status to 'completed' after all numbers are called
        mysqli_query($conn, "UPDATE games SET status = 'Game Completed' WHERE id = $gameId");
        echo "Game ID $gameId completed.\n";
    } else {
        echo "No game scheduled to start yet.\n";
    }
} else {
    echo "No scheduled games found.\n";
}

// Close the database connection
mysqli_close($conn);
