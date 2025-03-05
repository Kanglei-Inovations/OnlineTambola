<?php
header('Content-Type: application/json');

if (isset($_GET['game_id']) && is_numeric($_GET['game_id'])) {
    $game_id = (int)$_GET['game_id'];
    $pidFile = "game_$game_id.pid";
    $logFile = "game_$game_id.log";

    if (file_exists($pidFile)) {
        $pid = trim(file_get_contents($pidFile));

        // Check if process is still running
        $isRunning = false;
        if (stristr(PHP_OS, 'WIN')) {
            exec("tasklist /FI \"PID eq $pid\"", $output);
            $isRunning = count($output) > 1;
        } else {
            exec("ps -p $pid", $output);
            $isRunning = count($output) > 1;
        }

        if ($isRunning) {
            // Read the last 10 lines of the log file for progress
            $log = [];
            if (file_exists($logFile)) {
                $log = array_slice(file($logFile), -10);
            }
            echo json_encode(['running' => true, 'log' => $log]);
        } else {
            unlink($pidFile);  // Remove PID file if process is finished
            echo json_encode(['running' => false]);
        }
    } else {
        echo json_encode(['running' => false]);
    }
} else {
    echo json_encode(['error' => 'Invalid or missing game ID.']);
}
