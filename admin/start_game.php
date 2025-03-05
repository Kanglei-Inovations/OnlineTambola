<?php
header('Content-Type: application/json');

// Check if game_id is provided
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['game_id']) && is_numeric($input['game_id'])) {
    $game_id = (int)$input['game_id'];
    $batFile = "run_game.bat";
    $logFile = "game_$game_id.log";  // Log file to track progress

    // Create the batch command with output redirected to a log file
    $command = "$batFile $game_id > $logFile 2>&1 & echo $!";

    // Execute the batch file in the background
    $pid = shell_exec($command);

    if ($pid) {
        // Save PID to track the process
        file_put_contents("game_$game_id.pid", $pid);
        echo json_encode(['success' => true, 'message' => 'Game started successfully.', 'pid' => trim($pid)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to start game.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing game ID.']);
}
