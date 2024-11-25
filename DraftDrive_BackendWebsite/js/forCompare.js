// This function takes parameters of playerID and placeholderID from compare.php (it is called there.) and access t
// the placeholder there and shows the player name and pos there. 
function fetchPlayerData(playerId, placeholderId) {
    fetch(`fetch_playersForCompare.php?playerId=${playerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var placeholder = document.getElementById(placeholderId);
                placeholder.innerHTML = `
                    <div class="player-image-placeholder"></div>
                    <h3>${data.player.Player}</h3>
                    <h3>${data.player.teamCode}</h3>
                `;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Assigning events for dynamic player items
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.player-list-container').addEventListener('dragstart', (e) => {
        if (e.target.classList.contains('player-item')) {
            e.dataTransfer.setData('text/plain', e.target.dataset.playerid);
        }
    });

    // Dragover and drop events for placeholders
    document.querySelectorAll('.player-compare-placeholder').forEach(placeholder => {
        placeholder.addEventListener('dragover', event => event.preventDefault());
        placeholder.addEventListener('drop', event => {
            event.preventDefault();
            const playerId = event.dataTransfer.getData('text/plain');
            fetchPlayerData(playerId, placeholder.id);
        });
    });

    // If a default player ID is provided, fetch and display its data
    if (defaultPlayerId) {
        fetchPlayerData(defaultPlayerId, 'placeholder1');
    }
});

// This function is used to similar to the fetchPlayer function and is used to display the player details after they are dropped into the placeholder area.
function displayPlayerData(playerId, placeholder) {
    fetch(`fetch_playersForCompare.php?playerId=${playerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const playerInfo = data.player;
                const playerDetailsHtml = `
                    <div class="player-image-placeholder"></div>
                    <h3>${playerInfo.Player}</h3>
                    <p>Team: ${playerInfo.teamCode}</p>
                `;
                placeholder.innerHTML = playerDetailsHtml;
            } else {
                alert(data.message || 'Failed to fetch player data.');
            }
        })
        .catch(error => console.error('Error:', error));
}


const placeholders = document.querySelectorAll('.player-compare-placeholder');
placeholders.forEach(placeholder => {
    placeholder.addEventListener('dragover', event => {
        event.preventDefault();
    });

    placeholder.addEventListener('drop', event => {
        event.preventDefault();
        const playerId = event.dataTransfer.getData('text/plain');
        displayPlayerData(playerId, placeholder);
    });
});


