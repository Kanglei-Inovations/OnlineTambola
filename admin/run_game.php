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
// Check for winners after each number is called
checkWinners($conn, $gameId);

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

/**
 * Function to check for winning conditions
 */
function checkWinners($conn, $gameId) {
    // Fetch all called numbers for this game
    $calledNumbers = [];
    $res = mysqli_query($conn, "SELECT number FROM called_numbers WHERE game_id = $gameId");
    while ($row = mysqli_fetch_assoc($res)) {
        $calledNumbers[] = $row['number'];
    }

    // Track win types to avoid duplicate checks
    $winTypes = [
        'Top Line' => false,
        'Middle Line' => false,
        'Bottom Line' => false
    ];

    // Fetch all tickets for this game
    $tickets = mysqli_query($conn, "SELECT * FROM tickets WHERE game_id = $gameId");

    while ($ticket = mysqli_fetch_assoc($tickets)) {
        $ticketId = $ticket['id'];
        
        // Convert ticket numbers to array
        $ticketNumbers = json_decode($ticket['ticket'], true);
        if (!is_array($ticketNumbers)) {
            echo "Error: Ticket data format invalid for ticket ID $ticketId\n";
            continue;
        }

        if (!$winTypes['Top Line'] && checkTopLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Top Line', $gameId, $ticketId);
            $winTypes['Top Line'] = true;
        }

        if (!$winTypes['Middle Line'] && checkMiddleLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Middle Line', $gameId, $ticketId);
            $winTypes['Middle Line'] = true;
        }

        if (!$winTypes['Bottom Line'] && checkBottomLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Bottom Line', $gameId, $ticketId);
            $winTypes['Bottom Line'] = true;
        }

        // Stop checking if all win types have been found
        if ($winTypes['Top Line'] && $winTypes['Middle Line'] && $winTypes['Bottom Line']) {
            break;
        }
    }
}

function insertWinner($conn, $winType, $gameId, $ticketId) {
    $dateTime = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($conn, "INSERT INTO win (win_type, game_id, ticket_id, date_time) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siis", $winType, $gameId, $ticketId, $dateTime);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "Winner Found! Type: $winType, Ticket ID: $ticketId\n";
}

function checkTopLine($ticketNumbers, $calledNumbers) {
    $topRow = array_filter($ticketNumbers[0]);
    return empty(array_diff($topRow, $calledNumbers));
}

function checkMiddleLine($ticketNumbers, $calledNumbers) {
    $middleRow = array_filter($ticketNumbers[1]);
    return empty(array_diff($middleRow, $calledNumbers));
}

function checkBottomLine($ticketNumbers, $calledNumbers) {
    $bottomRow = array_filter($ticketNumbers[2]);
    return empty(array_diff($bottomRow, $calledNumbers));
}

?>