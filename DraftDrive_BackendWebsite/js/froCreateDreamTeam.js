document.addEventListener('DOMContentLoaded', function() {
    var teamSelectElement = document.getElementById('teamSelect');
    
    if (teamSelectElement) {
        teamSelectElement.addEventListener('change', function() {
            fetchPlayersForTeam(this.value);
        });
    }
});

//This function is used to asynchronously fetch player data to create_dreamteam.php via the fetch_players.php script. 
function fetchPlayersForTeam(teamCode) {
    if (teamCode) {
        //Sending the teamCode to fetch_players.php using the url.
        fetch('fetch_players.php?teamCode=' + encodeURIComponent(teamCode))
            .then(response => response.json())
            .then(players => {
                const playerCardsContainer = document.getElementById('playerCards');
                playerCardsContainer.innerHTML = ''; // Clear existing player cards

                // Add new player cards for each player
                players.forEach(player => {
                    const playerCard = document.createElement('div');
                    playerCard.className = 'player-card';
                    playerCard.draggable = true;
                    playerCard.textContent = player.Player + ' - ' + player.Pos; // The text to show on the card

                    // Set the player ID as the card's ID
                    playerCard.id = player.playerID;

                    // Add event listeners for the drag-and-drop functionality
                    playerCard.addEventListener('dragstart', handleDragStart);

                    // Append the new card to the container
                    playerCardsContainer.appendChild(playerCard);
                });
            })
            .catch(error => {
                console.error('Error fetching players:', error);
            });
    }
}

// This function is used to create player cards and specify their id, class and other attributes using the player data we obtain from fetch_players.php
function renderPlayerCards(players) {
    const playerCardsContainer = document.getElementById('playerCards');
    playerCardsContainer.innerHTML = ''; // Clear previous players

    players.forEach((player) => {
        const playerCard = document.createElement('div');
        playerCard.className = 'player-card';
        playerCard.draggable = true;
        playerCard.id = player.playerID;
        playerCard.textContent = `${player.Player} - ${player.Pos} (Teams: ${player.Teams})`;
        
        // Set the event listener. 
        playerCard.addEventListener('dragstart', (e) => {
            // Set the player name and teams as the drag data
            e.dataTransfer.setData('text/plain', playerCard.id);
        });
        playerCardsContainer.appendChild(playerCard);
    });
}


// Event handlers
function handleDragStart(e) {
  
    e.dataTransfer.setData('text/plain', e.target.id);
}

function handleDragOver(e) {
    e.preventDefault(); // Necessary to allow a drop
}

function handleDrop(e) {
    e.preventDefault(); // Prevent default behavior

    // Retrieve the ID of the dragged element
    const playerID = e.dataTransfer.getData('text/plain');
    const playerCard = document.getElementById(playerID);

    console.log(playerCard);
    if (playerCard && e.target.classList.contains('position')) {

            const playerCardClone = playerCard.cloneNode(true);     
            position = e.target.firstChild;
            console.log(position.textContent);
            var first = e.target.firstChild;
            while(first){
                first.remove();
                first=e.target.firstChild;
            }
            e.target.append(position);
            e.target.append(playerCardClone);
    } else {
        console.error('Player card not found:', playerID);
       
    }
}


// Add event listeners to the dream team placeholders
const positions = document.querySelectorAll('.position');
positions.forEach(position => {
    position.addEventListener('dragover', handleDragOver);
    position.addEventListener('drop', handleDrop);
});


// This function, along with the sendDreamTeamData() is used to save the dream team that user has made after they click the save button. 
function saveDreamTeam() {
    const positionElements = document.querySelectorAll('.position .player-card');
    let dreamTeamData = [];
    positionElements.forEach(el => {
        dreamTeamData.push(el.id); // el.id is now directly the playerId
    });
    console.log("Dream team data:", dreamTeamData);
    if (dreamTeamData.length === 5) {
        sendDreamTeamData(dreamTeamData);
    } else {
        alert("At least 5 players need to be selected");
    }
}

// This function takes data from the saveDreamTeam function and then uses the save_dreamteam.php
// to asynchronously save the dream teams into the database. 
function sendDreamTeamData(dreamTeamData) {
    console.log("JSON string to be sent:", JSON.stringify({ dreamTeam: dreamTeamData }));
    fetch('save_dreamteam.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ dreamTeam: dreamTeamData })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text(); // First get the response text
    })
    .then(text => {
        return JSON.parse(text); // Then parse it as JSON
    })
    .then(data => {
        console.log('Response data:', data); // Log the parsed JSON data
        if (data.success) {
            alert('Dream Team saved successfully!');
        } else {
            alert('Failed to save Dream Team. Message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
