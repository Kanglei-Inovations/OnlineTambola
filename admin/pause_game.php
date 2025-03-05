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

        // Pause the game process using SIGSTOP
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            // Windows doesn't support SIGSTOP; use taskkill to pause (requires PsSuspend)
            $command = "PsSuspend.exe $pid";
        } else {
            // Linux/Mac: Use kill -STOP
            $command = "kill -STOP $pid";
        }

        shell_exec($command);
        echo json_encode(['success' => true, 'message' => 'Game paused successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'PID file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}
