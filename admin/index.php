
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - Tambola Game</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<style>
    #tickets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.ticket-container {
    text-align: center;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    padding: 10px;
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
    margin-bottom: 10px;
}

.ticket-container td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

.ticket-container td.filled {
    background-color: #f0f0f0;
    font-weight: bold;
}

.ticket-container table,
.ticket-container td {
    max-width: 100%;
    word-wrap: break-word;
}

.countdown {
    font-size: 20px;
    margin: 10px 0;
}

</style>
</head>

<body class="bg-gray-100">

<!-- Header -->
<header class="bg-blue-600 text-white p-4 text-center text-2xl font-semibold shadow-md">
    Admin Panel - Tambola Game Online 🎲 
</header>

<div class="container mx-auto p-4">
    <!-- Schedule Game -->
    <section class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-bold mb-4">Schedule New Game 🗓️</h2>
        <form id="scheduleForm" class="flex items-center gap-2">
            <input type="datetime-local" id="gameTime" class="border p-2 rounded w-full" required>
            <input type="number" id="ticketCount" class="border p-2 rounded w-full" placeholder="Number of Tickets" min="1" required>
            <input type="number" id="ticket_price" class="border p-2 rounded w-full" placeholder="Price of Tickets" min="1" required>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Schedule Game
            </button>
        </form>
    </section>
    
    
</div>

    <!-- Scheduled Games -->
    <section class="bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-xl font-bold mb-4">Scheduled Games 🕒</h2>
        <table class="w-full border-collapse text-center">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Game ID</th>
                    <th class="border p-2">Scheduled Time</th>
                    <th class="border p-2">Ticket Count</th>
                    <th class="border p-2">Ticket Price</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Console</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="gamesList">
                <!-- Dynamic Game Rows -->
            </tbody>
        </table>
    </section>
    <section id="ticketContainer" class="bg-gray-100 py-8 hidden">
    <div class="container mx-auto px-4" id="ticketsummary">
        <!-- Ticket Summary Section -->
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">🎟️ Ticket Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Tickets -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="text-blue-500 text-4xl mr-4">🏷️</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Total Tickets</h3>
                        <p id="totalTickets" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <!-- Sold Tickets -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="text-green-500 text-4xl mr-4">🎫</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Sold Tickets</h3>
                        <p id="soldTickets" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <!-- Half-sheet Booked -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="text-yellow-500 text-4xl mr-4">📄</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Half-sheet Booked</h3>
                        <p id="halfSheetBooked" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <!-- Full-sheet Booked -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="text-purple-500 text-4xl mr-4">📑</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Full-sheet Booked</h3>
                        <p id="fullSheetBooked" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <!-- Tickets Left -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="text-red-500 text-4xl mr-4">🕒</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Tickets Left</h3>
                        <p id="ticketsLeft" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Booking Section -->
        <section id="ticketSection" class="bg-white p-4 rounded-lg shadow w-full mb-8">
            <h2 class="text-lg font-bold mb-4 text-center">Book Tickets</h2>
            <div id="ticketControls" class="mb-4 text-center"></div>
            <div id="ticketContent" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        </section>
    </div>
</section>



</div>


<script>
// Example Backend Endpoints (Adjust as needed)
const API = {
    getGames: 'get_scheduled_games.php',
    scheduleGame: 'schedule_game.php',
    updateGame: 'update_game.php',
    startGame: 'start_game.php',
    generateTickets: 'generate_tickets.php',
    getTickets: 'get_tickets.php',
    updateTickets: 'updateTickets.php',
    getTicketSummary: 'get_ticket_summary.php'  // 🆕 New API for ticket summary
};

// Fetch Scheduled Games
async function fetchScheduledGames() {
    try {
        const res = await axios.get(API.getGames);
        const games = res.data || [];
        const gamesList = document.getElementById('gamesList');
        gamesList.innerHTML = '';
        document.getElementById('ticketContainer').classList.remove('hidden');

        games.forEach(game => {
            const scheduledTime = new Date(game.scheduled_time).getTime();
            const now = new Date().getTime();
            const timeRemaining = scheduledTime - now;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="border p-2">${game.id}</td>
                <td class="border p-2">
                    <input type="datetime-local" value="${game.scheduled_time}" class="border p-1 rounded w-full" disabled
                           id="dateTime-${game.id}">
                </td>
                <td class="border p-2">
                    <input type="number" value="${game.ticket_count}" min="1" class="border p-1 rounded w-full" disabled
                           id="ticketCount-${game.id}">
                </td>
                <td class="border p-2">
                    <input type="number" value="${game.ticket_price}" min="1" class="border p-1 rounded w-full" disabled
                           id="ticket_price-${game.id}">
                </td>
                <td class="border p-2">${game.status}</td>
                <td class="border p-2" id="status-${game.id}">
                    <div id="logContainer-${game.id}" class="text-sm overflow-auto h-20"></div>
                    <div class="mt-1">
                        <div class="w-full bg-gray-200 rounded-full">
                            <div id="progressBar-${game.id}" class="bg-blue-500 text-xs leading-none py-1 text-center text-white rounded-full" style="width: 0%;">
                                0%
                            </div>
                        </div>
                    </div>
                    <div id="countdown-${game.id}" class="text-lg font-bold text-red-500 mt-2"></div> 
                </td>
                <td class="border p-2 flex justify-center gap-2">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600"
                            onclick="enableEdit(${game.id})" id="editBtn-${game.id}">Edit</button>
                    <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 hidden"
                            onclick="updateGame(${game.id})" id="updateBtn-${game.id}">Update</button>
                    <button class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600"
                            onclick="startGame(${game.id})">Start</button>
                  
                    <button class="bg-purple-500 text-white px-2 py-1 rounded hover:bg-purple-600"
                            onclick="loadTickets(${game.id})">Show Ticket</button>
                </td>
            `;
            gamesList.appendChild(row);

            // Start countdown if time is remaining
            if (timeRemaining > 0) {
                startCountdown(game.id, scheduledTime);
            } else {
                document.getElementById(`countdown-${game.id}`).innerHTML = "Game Started!";
                startGame(game.id);
            }
        });
    } catch (error) {
        console.error('Error fetching games:', error);
    }
}

function startCountdown(gameId, scheduledTime) {
    const countdownElement = document.getElementById(`countdown-${gameId}`);

    const interval = setInterval(() => {
        const now = new Date().getTime();
        const timeLeft = scheduledTime - now;

        if (timeLeft <= 0) {
            clearInterval(interval);
            countdownElement.innerHTML = "Game Started!";
            startGame(gameId);
        } else {
            const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((timeLeft / (1000 * 60)) % 60);
            const seconds = Math.floor((timeLeft / 1000) % 60);
            countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
        }
    }, 1000);
}
async function pauseGame(gameId) {
    try {
        const response = await axios.post('pause_game.php', {
            game_id: gameId
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            console.log('Game paused:', response.data);
        } else {
            console.error('Failed to pause game:', response.data.message);
        }
    } catch (error) {
        console.error('Error pausing game:', error);
    }
}

async function stopGame(gameId) {
    try {
        const response = await axios.post('stop_game.php', {
            game_id: gameId
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            console.log('Game stopped:', response.data);
        } else {
            console.error('Failed to stop game:', response.data.message);
        }
    } catch (error) {
        console.error('Error stopping game:', error);
    }
}

// Function to fetch logs and update log container
async function fetchLogs(gameId) {
    try {
        const res = await axios.get(`get_logs.php?game_id=${gameId}`);
        if (res.data.success) {
            const logContainer = document.getElementById(`logContainer-${gameId}`);
            logContainer.innerText = res.data.logs;  // Update log text
            logContainer.scrollTop = logContainer.scrollHeight;  // Auto-scroll to bottom
        }
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
}
// async function fetchLogs(gameId) {
//     try {
//         const res = await axios.get(`get_logs.php?game_id=${gameId}`);
//         if (res.data.success) {
//             const logContainer = document.getElementById(`logContainer-${gameId}`);
//             const logs = res.data.logs.trim().split('\n');  // Split logs into lines
//             const lastLog = logs[logs.length - 5];           // Get the last log line
//             logContainer.innerText = lastLog || "Waiting for updates...";  // Show last log line or a placeholder
//             logContainer.scrollTop = logContainer.scrollHeight;  // Auto-scroll to bottom if needed
//         }
//     } catch (error) {
//         console.error('Error fetching logs:', error);
//     }
// }


// Function to fetch progress and update progress bar
async function fetchProgress(gameId) {
    try {
        const res = await axios.get(`get_progress.php?game_id=${gameId}`);
        if (res.data.success) {
            const progressBar = document.getElementById(`progressBar-${gameId}`);
            const progress = res.data.progress;
            progressBar.style.width = `${progress}%`;
            progressBar.innerText = `${progress}%`;
        }
    } catch (error) {
        console.error('Error fetching progress:', error);
    }
}

// Function to start real-time updates
function startLiveUpdates(gameId) {
    // Fetch logs and progress every 2 seconds
    setInterval(() => {
        fetchLogs(gameId);
        fetchProgress(gameId);
    }, 1000);
}
// Function to start the game and begin live updates
async function startGame(gameId) {
    document.getElementById(`countdown-${gameId}`).classList.add('hidden');
    console.log(`Game ${gameId} started!`);
    try {
        const response = await axios.post('start_game.php', {
            game_id: gameId
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.data.success) {
            console.log('Game started:', response.data);
            startLiveUpdates(gameId);  // Start live updates after game starts
        } else {
            console.error('Failed to start game:', response.data.message);
        }
    } catch (error) {
        console.error('Error starting game:', error);
    }
}

let currentOffset = 0;
const limit = 50;  // Load 50 tickets per batch

// Load Tickets Function
async function loadTickets(gameId) {
    try {
        const res = await axios.get(`${API.getTickets}?game_id=${gameId}&limit=${limit}&offset=${currentOffset}`);
        let tickets = res.data || [];
        const ticketContent = document.getElementById('ticketContent');
        const ticketControls = document.getElementById('ticketControls');
        ticketContent.innerHTML = '';  // Clear previous tickets
        const ticketSummary = document.getElementById('ticketsummary');
        await updateTicketSummary(gameId);
        // Create controls for sorting and range input
        ticketControls.innerHTML = `
            <div class="mb-4 text-center">
                <label for="sortTickets" class="font-bold">Sort by Ticket No:</label>
                <select id="sortTickets" class="border p-1 rounded ml-2">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <input type="text" id="ticketRange" placeholder="e.g. 1-50 or 1" class="border p-1 rounded ml-2" />
                <button id="loadRangeBtn" class="border p-1 rounded bg-blue-500 text-white ml-2">Load Range</button>
            </div>
        `;

        // Function to render tickets
        const renderTickets = (tickets) => {
    ticketContent.innerHTML = '';  // Clear previous tickets
    tickets.forEach(ticket => {
        const parsedTicket = JSON.parse(ticket.ticket);
        const ticketContainer = document.createElement('div');
        ticketContainer.className = 'ticket-container border p-2 rounded shadow';

        // Create ticket HTML
        ticketContainer.innerHTML = `
            <h4 class="font-bold text-center mb-2">Ticket No: ${ticket.id}</h4>
            <input type="text" value="${ticket.player_name || ''}" placeholder="Enter Player Name" 
                   class="border p-1 rounded w-full mb-2"
                   id="playerName-${ticket.id}">
            <table class="w-full border-collapse mb-2">
                ${parsedTicket.map(row => `
                    <tr>
                        ${row.map(cell => `
                            <td class="border p-1 text-center ${cell === 0 ? '' : 'filled'}">
                                ${cell === 0 ? '' : cell}
                            </td>
                        `).join('')}
                    </tr>
                `).join('')}
            </table>
            <button onclick="saveIndividualTicket(${ticket.id})" 
                    class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 w-full">
                Booked
            </button>
        `;

        ticketContent.appendChild(ticketContainer);
    });
    };


        renderTickets(tickets);

        // Load more tickets button
        document.getElementById('loadMoreBtn')?.remove();
        ticketContent.insertAdjacentHTML('afterend', `
            <button id="loadMoreBtn" class="border p-1 rounded bg-green-500 text-white mt-4 mx-auto block">
                Load More
            </button>
        `);

        document.getElementById('loadMoreBtn').addEventListener('click', async () => {
            currentOffset += limit;
            const res = await axios.get(`${API.getTickets}?game_id=${gameId}&limit=${limit}&offset=${currentOffset}`);
            const moreTickets = res.data || [];
            renderTickets(moreTickets);
        });

        // Load tickets by range
        document.getElementById('loadRangeBtn').addEventListener('click', async () => {
            const range = document.getElementById('ticketRange').value.trim();
            if (range) {
                const res = await axios.get(`${API.getTickets}?game_id=${gameId}&range=${range}`);
                tickets = res.data || [];
                ticketContent.innerHTML = '';  // Clear previous tickets
                renderTickets(tickets);
            }
        });

        // Sorting event listener
        document.getElementById('sortTickets').addEventListener('change', (e) => {
            const sortOrder = e.target.value;
            tickets = tickets.sort((a, b) => {
                return sortOrder === 'asc' ? a.id - b.id : b.id - a.id;
            });
            ticketContent.innerHTML = '';  // Clear ticket grid
            renderTickets(tickets);
        });

        // Save tickets
        document.getElementById('saveTicketsBtn').onclick = () => saveTickets(tickets);
    } catch (error) {
        console.error('Error fetching tickets:', error);
        
    }
}
async function updateTicketSummary(gameId) {
    try {
        // Fetch summary data for the selected game
        const res = await axios.get(`${API.getTicketSummary}?game_id=${gameId}`);
        const summary = res.data;

        // Update summary in the UI
        document.getElementById('totalTickets').textContent = summary.totalTickets || 0;
        document.getElementById('soldTickets').textContent = summary.soldTickets || 0;
        document.getElementById('halfSheetBooked').textContent = summary.halfSheetBooked || 0;
        document.getElementById('fullSheetBooked').textContent = summary.fullSheetBooked || 0;
        document.getElementById('ticketsLeft').textContent = summary.ticketsLeft || 0;
    } catch (error) {
        console.error('Error fetching ticket summary:', error);
    }
}

// Save Individual Ticket
async function saveIndividualTicket(ticketId) {
    const playerName = document.getElementById(`playerName-${ticketId}`).value.trim() || '';

    try {
        const res = await axios.post(API.updateTickets, { 
            tickets: [{ id: ticketId, player_name: playerName }] 
        });
        alert(res.data.message || 'Ticket updated successfully!');
    } catch (error) {
        console.error('Error updating ticket:', error);
        alert('Failed to update ticket.');
    }
}



// Save Updated Tickets
async function saveTickets(tickets) {
    const updatedTickets = tickets.map(ticket => ({
        id: ticket.id,
        player_name: document.getElementById(`playerName-${ticket.id}`).value.trim() || ''
    }));

    try {
        const res = await axios.post(API.updateTickets, { tickets: updatedTickets });
        alert(res.data.message || 'Tickets updated successfully!');
        document.getElementById('ticketModal').classList.add('hidden');  // Hide modal
    } catch (error) {
        console.error('Error updating tickets:', error);
        alert('Failed to update tickets.');
    }
}

// Format ticket for display
function formatTicket(ticket) {
    return ticket.map(row => row.join('  ')).join('\n');
}



// Enable Edit Mode
function enableEdit(gameId) {
    // Enable inputs for editing
    document.getElementById(`dateTime-${gameId}`).disabled = false;
    document.getElementById(`ticketCount-${gameId}`).disabled = false;
    document.getElementById(`ticket_price-${gameId}`).disabled = false;
    // Show the Update button and hide the Edit button
    document.getElementById(`editBtn-${gameId}`).classList.add('hidden');
    document.getElementById(`updateBtn-${gameId}`).classList.remove('hidden');
}

// Update Game Details
async function updateGame(gameId) {
    const newTime = document.getElementById(`dateTime-${gameId}`).value;
    const newTicketCount = document.getElementById(`ticketCount-${gameId}`).value;
    const newTicketPrice = document.getElementById(`ticket_price-${gameId}`).value;  // ✅ Added this line

    if (!newTime || newTicketCount <= 50 || newTicketPrice <= 0) {  // ✅ Check ticket price validity
        alert('Please enter a valid date, time, ticket count not less than 50, and a valid ticket price');
        return;
    }

    try {
        const formData = new URLSearchParams();
        formData.append('game_id', gameId);
        formData.append('scheduled_time', newTime);
        formData.append('ticket_count', newTicketCount);
        formData.append('ticket_price', newTicketPrice);  // ✅ Corrected this line

        const res = await axios.post(API.updateGame, formData);
        alert(res.data.message || 'Game details updated!');

        // Disable inputs and switch buttons back
        document.getElementById(`dateTime-${gameId}`).disabled = true;
        document.getElementById(`ticketCount-${gameId}`).disabled = true;
        document.getElementById(`ticket_price-${gameId}`).disabled = true;
        document.getElementById(`editBtn-${gameId}`).classList.remove('hidden');
        document.getElementById(`updateBtn-${gameId}`).classList.add('hidden');

        fetchScheduledGames(); // Refresh list
    } catch (error) {
        console.error('Error updating game:', error);
        alert('Failed to update game details.');
    }
}



// Schedule Game
document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const gameTime = document.getElementById('gameTime').value;
    const ticketCount = document.getElementById('ticketCount').value;
    const ticket_price = document.getElementById('ticket_price').value;
    if (!gameTime || !ticketCount) return alert('Please select time and enter the number of tickets!');

    try {
        await axios.post(API.scheduleGame, new URLSearchParams({ scheduled_time: gameTime, ticket_count: ticketCount, ticket_price:ticket_price}));
        fetchScheduledGames();
        alert('Game scheduled successfully!');
    } catch (error) {
        console.error('Error scheduling game:', error);
        alert('Failed to schedule game.');
    }
});


// Initial Fetch
fetchScheduledGames();
</script>

</body>
</html>
