<?php
/* 
This is the player-detail.php. In this script, when a user selects a player from the 
team detail page, they are brought to this page. They can see the details of that particular player 
and they also have the option of compare that player to another player through a compare button.
*/

include "functions.php";
include "header.php";
include "connectionInfo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['playerId'])) {
    $playerId = $_POST['playerId'];

    $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);

    // Fetch player details
    $stmt = $conn->prepare("SELECT * FROM Players WHERE Player = ?;");
    $stmt->bind_param("s", $playerId);
    $stmt->execute();
    $playerResult = $stmt->get_result();

    if ($playerResult->num_rows > 0) {
        $player = $playerResult->fetch_assoc();

        echo '<div class="player">';
        echo '<div class="player-image-placeholder"></div>'; // Placeholder for player image
        echo '<h3>' . htmlspecialchars($player['Player']) . '</h3>'; // Display player name
        echo '<h3>' . htmlspecialchars($player['teamCode']) . '</h3>'; // Display team 
        echo '</div>'; // Close player div


        
        echo '<div class="player-stats">';

        //Here, if the user clicks the compare button, the playerID is sent as post to compare.php. 
        echo '<form action="compare.php" method="post">';
        echo '<input type="hidden" name="playerId" value="' . htmlspecialchars($player['playerId']) . '">';
        echo '    <button type="submit" id="compareButton">Compare Player';


        echo '</button>';
        echo '</form>';
        
        // Display stats
        $stats = ['Pos', 'Age', 'PTS', 'FG', 'FG%', '3P', '3P%', '2P', '2P%', 'FT', 'FT%', 'ORB', 'DRB', 'AST', 'STL', 'BLK', 'TOV', '3P%']; // Add more stat keys as needed
        foreach ($stats as $stat) {
            if (isset($player[$stat]) && $player[$stat] !== null) {
                $value = htmlspecialchars($player[$stat]);
                if (str_ends_with($stat, '%')) {
                    $value .= '%'; // Append a percentage sign
                }
            } else {
                $value = 'N/A';
            }
            echo '<p>' . $stat . ': ' . $value . '</p>';
        }
        echo '</div>'; // Close player-stats div
    } else {
        echo '<p>No player found with ID: ' . htmlspecialchars($playerId) . '</p>';
    }
    $conn->close();
} else {
    echo '<p>No player specified.</p>';
}
?>
