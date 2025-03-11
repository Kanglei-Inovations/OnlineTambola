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

    // Fetch existing winners from the database
    $existingWinners = [];
    $winRes = mysqli_query($conn, "SELECT win_type, ticket_id FROM win WHERE game_id = $gameId");
    while ($row = mysqli_fetch_assoc($winRes)) {
        $existingWinners[$row['win_type']][] = $row['ticket_id'];
    }

    // Initialize tracking for new winners
    $winTypes = [
        'Early Five' => isset($existingWinners['Early Five']),
        'Top Line' => isset($existingWinners['Top Line']),
        'Middle Line' => isset($existingWinners['Middle Line']),
        'Bottom Line' => isset($existingWinners['Bottom Line']),
        'Star' => isset($existingWinners['Star'])
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

        // Check each win condition only if not already won
        if (!$winTypes['Early Five'] && !in_array($ticketId, $existingWinners['Early Five'] ?? []) && checkEarlyFive($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Early Five', $gameId, $ticketId);
            $winTypes['Early Five'] = true;
        }
        if (!$winTypes['Top Line'] && !in_array($ticketId, $existingWinners['Top Line'] ?? []) && checkTopLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Top Line', $gameId, $ticketId);
            $winTypes['Top Line'] = true;
        }
        if (!$winTypes['Middle Line'] && !in_array($ticketId, $existingWinners['Middle Line'] ?? []) && checkMiddleLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Middle Line', $gameId, $ticketId);
            $winTypes['Middle Line'] = true;
        }
        if (!$winTypes['Bottom Line'] && !in_array($ticketId, $existingWinners['Bottom Line'] ?? []) && checkBottomLine($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Bottom Line', $gameId, $ticketId);
            $winTypes['Bottom Line'] = true;
        }
        if (!$winTypes['Star'] && !in_array($ticketId, $existingWinners['Star'] ?? []) && checkStarPattern($ticketNumbers, $calledNumbers)) {
            insertWinner($conn, 'Star', $gameId, $ticketId);
            $winTypes['Star'] = true;
        }

        // Stop checking if all win types have been found
        if ($winTypes['Early Five'] && $winTypes['Top Line'] && $winTypes['Middle Line'] && $winTypes['Bottom Line'] && $winTypes['Star']) {
            break;
        }
    }
}


function insertWinner($conn, $winType, $gameId, $ticketId) {
    $dateTime = date('Y-m-d H:i:s');

    // Check if the winner already exists in the database
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM win WHERE win_type = ? AND game_id = ? AND ticket_id = ?");
    mysqli_stmt_bind_param($checkStmt, "sii", $winType, $gameId, $ticketId);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($count == 0) {
        // Insert only if the record does not already exist
        $stmt = mysqli_prepare($conn, "INSERT INTO win (win_type, game_id, ticket_id, date_time) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "siis", $winType, $gameId, $ticketId, $dateTime);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "Winner Found! Type: $winType, Ticket ID: $ticketId\n";
    } else {
        echo "Winner Already Exists! Type: $winType, Ticket ID: $ticketId\n";
    }
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
function checkEarlyFive($ticketNumbers, $calledNumbers) {
    $allNumbers = [];
    
    // Flatten ticket array into a single list of numbers
    foreach ($ticketNumbers as $row) {
        foreach ($row as $num) {
            if ($num != 0) { // Ignore empty spaces
                $allNumbers[] = $num;
            }
        }
    }

    // Count how many numbers have been called
    $markedNumbers = array_intersect($allNumbers, $calledNumbers);
    return count($markedNumbers) >= 5;
}

function checkStarPattern($ticketNumbers, $calledNumbers) {
    // Star pattern: X shape on the ticket
    $starPositions = [
        [0, 0], [0, 8], // Top-left and top-right
        [1, 4],         // Center
        [2, 0], [2, 8]  // Bottom-left and bottom-right
    ];

    foreach ($starPositions as [$row, $col]) {
        if (!in_array($ticketNumbers[$row][$col], $calledNumbers)) {
            return false;
        }
    }
    return true;
}

?>