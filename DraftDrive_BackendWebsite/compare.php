<?php
/*
This is the compare.php page. Here the user can compare between two players of their choice from the list of all players. 
*/

include "connectionInfo.php";
include "functions.php";
include "header.php";

session_start();

$defaultPlayerId = isset($_POST['playerId']) ? $_POST['playerId'] : null; // Obtain player id

$conn = connectToDB($dbServer,$dbUser,$dbPass,$dbName); // Database connection.


echo '<!DOCTYPE html>'
. '<html lang="en">'
. '<head>'
. '<meta charset="UTF-8">'
. '<title>Compare Players</title>'
. '<link rel="stylesheet" href="./css/comparePlayers.css">'
. '</head>'
. '<body>';

// Empty placeholders for user to drop their choice of player
echo '<div class="player-compare-placeholder" id="placeholder1"></div>';
echo '<div class="player-compare-placeholder" id="placeholder2"></div>';

// Player list. This is a scrollable list of players 
echo '<div class="player-list-container">';

$result = $conn->query("SELECT DISTINCT Player, playerId FROM Players;");
while ($player = $result->fetch_assoc()) {
    //This below line accesses the js function (ajax) that firstly populate the container with the players using the fetch_playersForCompare and then also help with the drag and drop interface.
    echo "<div class='player-item' draggable='true' data-playerid='" . $player['playerId'] . "'>" . htmlspecialchars($player['Player']) . "</div>";
}
echo '</div>';
//Connecting to the js file. 
echo '<script src="./js/forCompare.js"></script>';

// Echo the defaultPlayerId variable inside the script tag. This line is so that when the user arrives to the compare from a pre selected player from the team deatils page, the selected player is defaulted to left placeholder.
echo "<script>
    var defaultPlayerId = " . json_encode($defaultPlayerId) . ";
    // If a default player ID is provided, fetch and display its data
    if (defaultPlayerId) {
        fetchPlayerData(defaultPlayerId, 'placeholder1');
    }
</script>";

echo '</body></html>';
$conn->close();
?>

