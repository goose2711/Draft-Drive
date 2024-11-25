<?php
/* 
This is the team-detail.php. In this script, when a user selects a team from the 
index page, they are brought to this page. They can see the players that belong to that team 
in more detail
*/
include "functions.php";
include "header.php";
include "connectionInfo.php";



if (isset($_GET['teamCode'])) {
    
    $teamCode = $_GET['teamCode'];
    $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);

    $displayedPlayers = array();

    // Fetch team details using the teamCode
    $stmt = $conn->prepare("SELECT name FROM Teams WHERE teamCode = ?");
    $stmt->bind_param("s", $teamCode);
    $stmt->execute();
    $teamResult = $stmt->get_result();

    if ($teamResult->num_rows > 0) {
        $team = $teamResult->fetch_assoc();
        echo "<h1>" . htmlspecialchars($team['name']) . "</h1>";

        // Fetch players for the team using the teamCode
        $playerStmt = $conn->prepare("SELECT Player, Pos, Age, FG, `FG%`, 3P, `3P%` FROM Players WHERE teamCode = ?");
        $playerStmt->bind_param("s", $teamCode);
        $playerStmt->execute();
        $playerResult = $playerStmt->get_result();

        if ($playerResult->num_rows > 0) {
            echo '<div class="player-list">';
            while ($player = $playerResult->fetch_assoc()) {
                if (!in_array($player['Player'], $displayedPlayers)) 
                {
                    // echo '<div class="player">';
                    // echo '<a href="player-detail.php">';
                    // echo '<div class="player-image-placeholder"></div>'; // Placeholder for player image
                    // echo '<h3>' . htmlspecialchars($player['Player']) . '</h3>';
                    // echo '</a>';
                    // echo '<div class="player-stats">';

                    echo '<div class="player">';
                    echo '<form action="player-detail.php" method="post">';
                    echo '<div class="player-image-placeholder"></div>'; // Placeholder for player image
                    echo '<input type="hidden" name="playerId" value="' . htmlspecialchars($player['Player']) . '">';
                    echo '<button type="submit" class="player-link">';
                    echo '<h3>' . htmlspecialchars($player['Player']) . '</h3>';
                    echo '</button>';
                    echo '</form>';
                    echo '<div class="player-stats">';
                    // Display stats with conditional checks
                    $stats = ['Pos', 'Age', 'FG', 'FG%', '3P', '3P%']; // Add more stat keys as needed
                    foreach ($stats as $stat) {
                        $value = isset($player[$stat]) ? htmlspecialchars($player[$stat]) : 'N/A';
                        if (str_ends_with($stat, '%')) {
                            $value = htmlspecialchars($player[$stat] * 100) . '%'; // Format percentage stats
                        }
                        echo '<p>' . $stat . ': ' . $value . '</p>';
                    }
                    echo '</div>';
                    echo '</div>'; // Close player div
                    $displayedPlayers[] = $player['Player'];
                }
            }
            echo '</div>'; // Close player-list div
        } else {
            echo "<p>No players found for this team.</p>";
        }
    } else {
        echo "<p>Team not found.</p>";
    }
    $conn->close();
} else {
    echo "<p>No team specified.</p>";
}
?>
