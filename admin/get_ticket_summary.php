<?php
header('Content-Type: application/json');
require 'db_connection.php';  // Ensure this file contains your database connection

if (isset($_GET['game_id'])) {
    $gameId = intval($_GET['game_id']);

    // 🟢 Fetch total tickets for the game
    $totalTicketsQuery = "SELECT COUNT(*) as total FROM tickets WHERE game_id = $gameId";
    $totalTicketsResult = mysqli_query($conn, $totalTicketsQuery);
    $totalTickets = mysqli_fetch_assoc($totalTicketsResult)['total'] ?? 0;

    // 🟢 Fetch sold tickets with non-empty player_name
    $soldTicketsQuery = "
        SELECT player_name, COUNT(*) as ticket_count 
        FROM tickets 
        WHERE game_id = $gameId AND player_name != '' 
        GROUP BY player_name
    ";
    $soldTicketsResult = mysqli_query($conn, $soldTicketsQuery);

    $soldTickets = 0;
    $halfSheetBooked = 0;
    $fullSheetBooked = 0;

    // 🟢 Calculate sold tickets, half-sheet, and full-sheet counts
    while ($row = mysqli_fetch_assoc($soldTicketsResult)) {
        $ticketCount = (int)$row['ticket_count'];
        $soldTickets += $ticketCount;  // Sum sold tickets

        // Calculate full sheets first
        if ($ticketCount >= 6) {
            $fullSheetBooked += intdiv($ticketCount, 6);  // Count full sheets
            $ticketCount %= 6;  // Remaining tickets after full sheets
        }
        // Calculate half sheets from remaining tickets
        if ($ticketCount >= 3) {
            $halfSheetBooked += intdiv($ticketCount, 3);  // Count half sheets
        }
    }

    // 🟢 Calculate tickets left
    $ticketsLeft = $totalTickets - $soldTickets;

    // 🟢 Return JSON response
    echo json_encode([
        'totalTickets' => (int)$totalTickets,
        'soldTickets' => (int)$soldTickets,
        'halfSheetBooked' => (int)$halfSheetBooked,
        'fullSheetBooked' => (int)$fullSheetBooked,
        'ticketsLeft' => max((int)$ticketsLeft, 0)  // Ensure no negative value
    ]);
} else {
    echo json_encode(['error' => 'Game ID is required']);
}
