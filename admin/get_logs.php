<?php
header('Content-Type: application/json');

// Check if game_id is provided
if (isset($_GET['game_id']) && is_numeric($_GET['game_id'])) {
    $game_id = (int)$_GET['game_id'];
    $logFile = "log/game_$game_id.log";

    // Check if log file exists
    if (file_exists($logFile)) {
        // Read the log file
        $logs = file_get_contents($logFile);
        echo json_encode(['success' => true, 'logs' => $logs]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Log file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}
