<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tambola_game';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


// Fetch tickets from the database
$tickets = $conn->query("SELECT * FROM tickets");
$allTickets = [];
while ($row = $tickets->fetch_assoc()) {
    $allTickets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<title>Tambola Game</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
        text-align: center;
    }
    h2, h3 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        margin: 10px auto;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }
    td {
        width: 40px;
        height: 40px;
        text-align: center;
        border: 1px solid #333;
        transition: background-color 0.3s, transform 0.2s;
    }
    .filled { background-color: #f0f0f0; }
    .marked { color: white; background-color:rgb(219, 20, 20) !important; }

    #tickets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
        }
        
    .countdown { font-size: 20px; margin: 10px 0; }
    .ticket-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 10px;
    transition: transform 0.3s;
    max-width: 300px;  /* Optional: Limit card width for a cleaner layout */
    margin: auto;  /* Center align the cards */
}
.ticket-container:hover {
    transform: scale(1.1);
    cursor: pointer;
}

.ticket-container h4 {
    margin-bottom: 10px;
    font-size: 18px;
    color: #333;
}

.ticket-container table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

.ticket-container td {
    border: 1px solid #ddd;
    padding: 5px;  /* Reduced padding for a tighter look */
    text-align: center;
    width: 40px;  /* Ensures all cells have the same size */
    height: 40px;  /* Ensures all cells have the same size */
    background-color: #f9f9f9;  /* Light background for contrast */
    transition: background-color 0.3s;
}

.ticket-container td.filled {
    
    background-color: #e0e0e0;  /* Different shade for filled cells */
}

.ticket-container td.marked {
    background-color: #db1414 !important;  /* Red for marked cells */
    color: #fff;
}

.ticket-container td:hover {
    background-color: #dcdcdc;  /* Hover effect for cells */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .ticket-container {
        max-width: 90%;  /* Make cards fit smaller screens */
    }
    .ticket-container td {
        width: 30px;
        height: 30px;
    }
}
.countdown {
        font-size: 24px;
        font-weight: bold;
        color: #ff5722;
        text-align: center;
        margin-top: 20px;
        background-color: #fff;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }
#number-board {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
    gap: 5px;
    margin: 10px;
    max-width: 100%;
    justify-content: center;
}

.num-cell {
        width: 40px;
        height: 40px;
        text-align: center;
        line-height: 40px;
        border: 1px solid #333;
        background-color: #fff;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
        
    }

   
    .marked-on-board {
        background-color: #87cefa !important;
        color: #fff;
        font-weight: bold;
        transform: scale(1.1);
    }
    .voice-btn {
        background-color: #4CAF50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s, transform 0.2s;
        margin: 20px;

    }
    .voice-btn.inactive {
        background-color: #f44336;
    }
    .voice-btn:hover {
        transform: scale(1.05);
    }
   form {
        margin: 20px;
    }
    form input {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-right: 5px;
    }
    form button {
        padding: 5px 10px;
        border: none;
        background-color: #4CAF50;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    form button:hover {
        background-color: #45a049;
    }
/* Responsive adjustments for smaller screens */
@media (max-width: 768px) {
    .num-cell {
        width: 35px;
        height: 35px;
        line-height: 35px;
    }
}

@media (max-width: 480px) {
    .num-cell {
        width: 30px;
        height: 30px;
        line-height: 30px;
    }
}
.countdown {
    font-size: 24px;
    font-weight: bold;
    color: #ff5722;
    text-align: center;
    margin-top: 20px;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.voice-btn {
        background-color: #4CAF50;  /* Active: Green */
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .voice-btn.inactive {
        background-color: #f44336; /* Inactive: Red */
    }

    .voice-btn:active {
        transform: scale(0.95);
    }

    .voice-btn i {
        transition: transform 0.3s;
    }

    .voice-btn.inactive i {
        transform: rotate(-90deg);
    }
</style>
</head>
<body>

<h2>Tambola Game</h2>



<center><button id="toggle-voice" class="voice-btn" onclick="toggleVoice()">
    <i class="fas fa-volume-up"></i> Voice Active
</button></center>
<form method="get">
    <input type="number" name="game_id" min="1" placeholder="Enter Game ID">
    <button type="submit">Show Tickets</button>
</form>
<br>
    
<?php 
if (isset($_GET['game_id']) && is_numeric($_GET['game_id'])) {
    ?>
<div class="countdown" id="countdown">Loading...</div>

<!-- Number Grid for 1–99 -->
<h3>Number Board:</h3>
<div id="number-board"></div>
<h3>Called Numbers: <span id="called"></span></h3>


<h3>Tickets:</h3>

<div id="tickets">
    <?php foreach ($allTickets as $ticket): ?>
        <div class="ticket-container">
            <h4>Ticket No: <?= htmlspecialchars($ticket['id']) ?> - (<?= htmlspecialchars($ticket['player_name']) ?>) </h4>
            <table>
                <?php
                $ticketData = json_decode($ticket['ticket']);
                foreach ($ticketData as $row) {
                    echo "<tr>";
                    foreach ($row as $num) {
                        echo "<td class='filled' data-num='$num'>" . ($num ?: '') . "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    <?php endforeach; ?>
</div>
<?php }?>


<script>
document.addEventListener("DOMContentLoaded", () => {
    // Create the number grid (1–99) once the DOM is fully loaded
    const numberBoard = document.getElementById("number-board");
    for (let i = 1; i <= 99; i++) {
        const numCell = document.createElement("div");
        numCell.textContent = i;
        numCell.className = "num-cell";
        numCell.id = `num-${i}`;
        numberBoard.appendChild(numCell);
    }
});


let calledNumbers = [];
let currentGameId = <?php echo $_GET['game_id'];?>;
let startTime = null;
// Toggle voice on/off
function toggleVoice() {
        voiceActive = !voiceActive;  // Toggle the state
        const btn = document.getElementById('toggle-voice');
        const icon = btn.querySelector('i');

        if (voiceActive) {
            btn.classList.remove('inactive');
            btn.innerHTML = '<i class="fas fa-volume-up"></i> Voice Active';
        } else {
            btn.classList.add('inactive');
            btn.innerHTML = '<i class="fas fa-volume-mute"></i> Voice Muted';
        }
    }


let countdownInterval;  // Declare countdownInterval globally

// Function to fetch game time and start countdown
function fetchGameTime(gameId) {
    console.log("Response:", gameId); 
    fetch('get_game_times.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `game_id=${encodeURIComponent(gameId)}`
    })
    .then(response => response.json())
    .then(data => {
        // Log the response
        if (data.error) {
            console.error(data.error);
            document.getElementById("countdown").textContent = "Error loading game time";
            return;
        }

        const startTime = new Date(data.start_time).getTime();
        const now = Date.now();

        if (now >= startTime) {
            clearInterval(countdownInterval);
            document.getElementById("countdown").textContent = data.status;
        } else {
            startCountdown(startTime);  // Start countdown if the game hasn't started
        }
    })
    .catch(error => {
        console.error("Error fetching game time:", error);
        document.getElementById("countdown").textContent = "Error loading game time";
    });
}


// Beautified countdown function
function startCountdown(startTime) {
    clearInterval(countdownInterval);  // Clear any previous intervals

    countdownInterval = setInterval(() => {
        const now = Date.now();
        const diff = startTime - now;

        if (diff <= 0) {
            clearInterval(countdownInterval);
            document.getElementById("countdown").textContent = "Game Starting...";
            setTimeout(() => {
                document.getElementById("countdown").textContent = "Game Started";
            }, 2000);  // Show "Game Starting..." briefly
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        const formattedCountdown = `${days > 0 ? days + ' day ' : ''}${hours > 0 ? hours + ' hr ' : ''}${minutes > 0 ? minutes + ' min ' : ''}${seconds > 0 ? seconds + ' sec' : ''}`;
        document.getElementById("countdown").textContent = formattedCountdown.trim();
    }, 1000);
}



// Automatically fetch game time every 5 seconds
setInterval(fetchGameTime(currentGameId), 5000);
fetchGameTime(currentGameId);  // Initial fetch to start countdown immediately




let lastMarkedIndex = -1;  // Keep track of the last marked number index
let voiceActive = true; 

// Call number with voice
function callNumberWithVoice(num) {
    if (!voiceActive) return Promise.resolve();  // Exit if voice is deactivated

    const intros = [
        "Calling number",
        "Next is",
        "Here we go",
        "Get ready for",
        "Watch out for",
        "And the next number is"
    ];
    const intro = intros[Math.floor(Math.random() * intros.length)];
    let phrase = "";

    if (num >= 1 && num <= 9) {
        phrase = `Single No ${num}`;
    } else if (num >= 10 && num <= 99) {
        const digits = num.toString().split("");
        const tens = digits[0];
        const ones = digits[1];
        phrase = tens === ones ? `${tens} and ${ones}, ${num}` : `${tens} and ${ones}, ${num}`;
    } else if (num === 100) {
        phrase = "Century, 100!";
    } else {
        phrase = `Number ${num}`;
    }

    return new Promise((resolve) => {
        let introUtterance = new SpeechSynthesisUtterance(`${intro}...`);
        let mainUtterance = new SpeechSynthesisUtterance(phrase);

        introUtterance.onend = () => {
            setTimeout(() => {
                window.speechSynthesis.speak(mainUtterance);
            }, 800);
        };

        mainUtterance.onend = () => resolve();  // Resolve promise when main utterance ends
        window.speechSynthesis.speak(introUtterance);
        markNumbers(num);         // Mark numbers on the tickets
        markNumberOnBoard(num);
    });
}
// Function to fetch and update called numbers live
function fetchAndUpdateCalledNumbers() {
    if (!currentGameId) {
        console.error("Game ID not set.");
        return;
    }

    fetch('get_called_numbers.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `game_id=${encodeURIComponent(currentGameId)}` // Send game ID
    })
    .then(response => response.json())
    .then(data => {
        if (data.called_numbers && data.called_numbers.length > lastMarkedIndex + 1) {
    const calledNumbers = data.called_numbers;
    for (let i = lastMarkedIndex + 1; i < calledNumbers.length; i++) {
        const num = calledNumbers[i];
        callNumberWithVoice(num); // Optional: Call number with voice
           // Mark numbers on the number board
        
    }
    lastMarkedIndex = calledNumbers.length - 1;
}

    })
    .catch(error => console.error("Error fetching called numbers:", error));
}

// Run fetchAndUpdateCalledNumbers every 5 seconds
setInterval(fetchAndUpdateCalledNumbers, 5000);
fetchAndUpdateCalledNumbers();  // Initial call to start immediately

function markNumbers(num) {
    let cells = document.querySelectorAll(`td[data-num='${num.toString()}']`);
    cells.forEach(cell => cell.classList.add('marked'));
}

function markNumberOnBoard(num) {
    const numCell = document.getElementById(`num-${num}`);
    if (numCell) {
        numCell.classList.add('marked-on-board');
    }
}

</script>

</body>
</html>
