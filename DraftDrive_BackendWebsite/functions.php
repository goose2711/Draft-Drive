<?php
/*
This php file is used to store all of the functions. 
*/

//This function is to dispaly the search bar 
function deploySearchBar()
{
    echo '
<div class="search-bg">
    <img src="images/nbabg.jpg" alt="bg" class="bg">
    <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search...">
        <button class="search-button">🔍</button>
        <!-- add a function to make the button search the team name -->
    </div>
</div>';

}

//This function is used to start a connection to the database
function connectToDB($dbhost, $dbuser, $dbpass, $dbname) 
{
    $conn = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    //did connection occur database?
    if (mysqli_connect_errno()) 
    {
        //quit and display error and error number
        die("Database connection failed:" .
            mysqli_connect_error() .
            " (" . mysqli_connect_errno() . ")"
        );
    }
    return $conn;
}

//This function is used to show the list of teams in the current season and also the list of players in that team. 
function deployTeamToggle($conn) 
{
    $displayedPlayers = array(); // Array to keep track of displayed players

    // Fetch the teams from the database
    $teamQuery = "SELECT teamCode, name FROM Teams";
    $teamResult = $conn->query($teamQuery);
    // Check if we have teams
    if ($teamResult->num_rows > 0) 
    {
        echo '<div class="team-list">';

        // Output data of each team
        while($team = $teamResult->fetch_assoc()) 
        {
            echo '<div class="team">';
            echo '<a href="team-detail.php?teamCode=' . urlencode($team['teamCode']) . '" class="team-name">';
            echo '<h3>' . htmlspecialchars($team['name']) . '</h3>';
            echo '</a>';
            echo '<button class="dropdown-button" onclick="toggleDropdown(\'' . htmlspecialchars($team['teamCode']) . '\')">▼</button>';
            echo '<div class="player-grid" id="' . htmlspecialchars($team['teamCode']) . '" style="display: none;">';

            // Fetch the players for the corresponding teamCode
            $playerQuery = "SELECT Player FROM Players WHERE teamCode = '" . $conn->real_escape_string($team['teamCode']) . "'";
            $playerResult = $conn->query($playerQuery);

            if ($playerResult->num_rows > 0) 
            {
                // Output data of each player
                while($player = $playerResult->fetch_assoc()) 
                {
                    if (!in_array($player['Player'], $displayedPlayers)) 
                    {
                        // echo '<form action="player-detail.php" method="post">';
                        // echo '<button class="player">';
                        
                        // echo htmlspecialchars($player['Player']);
                       
                        
                        // echo '</button>';

                        // echo '</form>';

                    echo '<form action="player-detail.php" method="post">';
                 
                    echo '<input type="hidden" name="playerId" value="' . htmlspecialchars($player['Player']) . '">';
                    echo '<button type="submit" class="player-link">';
                    echo '<h3>' . htmlspecialchars($player['Player']) . '</h3>';
                    echo '</button>';
                    echo '</form>';


                        $displayedPlayers[] = $player['Player']; // Add to the displayed players array
                    }
                }
            }
            echo '</div>'; // Close player-grid
            echo '</div>'; // Close team
        }
        echo '</div>'; // Close team-list
    } else 
    {
        echo "0 results";
    }  

}

?>