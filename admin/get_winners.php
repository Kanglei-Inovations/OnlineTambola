<?php
// Database connection
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}
$gameId = $_GET['game_id'] ?? 0;

if ($gameId > 0) {
    $query = "SELECT w.win_type, w.ticket_id, COALESCE(t.player_name, 'Unknown') AS player_name
    FROM win w 
    JOIN tickets t ON w.ticket_id = t.id
    WHERE w.game_id = ?";

    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $gameId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $winners = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $winners[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    echo json_encode(['success' => true, 'winners' => $winners]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid game ID']);
}
?>
