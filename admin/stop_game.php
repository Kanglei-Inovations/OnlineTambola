<?php
header('Content-Type: application/json');

// Check if game_id is provided
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['game_id']) && is_numeric($input['game_id'])) {
    $game_id = (int)$input['game_id'];
    $pidFile = "game_$game_id.pid";

    // Check if PID file exists
    if (file_exists($pidFile)) {
        $pid = trim(file_get_contents($pidFile));

        // Stop the game process
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            // Windows: Use taskkill
            $command = "taskkill /F /PID $pid";
        } else {
            // Linux/Mac: Use kill
            $command = "kill -9 $pid";
        }

        shell_exec($command);

        // Delete PID and log files after stopping
        unlink($pidFile);
        unlink("game_$game_id.log");

        echo json_encode(['success' => true, 'message' => 'Game stopped successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'PID file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}
