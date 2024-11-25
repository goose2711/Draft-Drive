<?php
/*
This is the create-dreamteam.php page. Here the user can drag and drop their favourite players from their favourite teams and create their very own dream team. 
*/
    include "functions.php";
    include "header.php";
    include "connectionInfo.php";
    
    session_start();

    $conn = connectToDB($dbServer,$dbUser,$dbPass,$dbName); // Start a connection to the database. 


    // Obtain the teams from the database
    $teamQuery = "SELECT teamCode, name FROM Teams";
    $teamResult = $conn->query($teamQuery);

    // Initialize an array to hold team options
    $teamsOptions = '';

    //Create an option tag based on the obtained team codes. 
    if ($teamResult && $teamResult->num_rows > 0) {
        while ($team = $teamResult->fetch_assoc()) {
            $teamsOptions .= "<option value=\"" . htmlspecialchars($team['teamCode']) . "\">" . htmlspecialchars($team['name']) . "</option>";
        }
    } else {
        $teamsOptions .= "<option value=''>No teams found</option>";
    }

    // Close connection
    $conn->close();

// this is creating an outline of the html with the placeholders and the team selection dropdpown. 
    echo '<!DOCTYPE html>'
    . '<html lang="en">'
    . '<head>'
    . '<meta charset="UTF-8">'
    . '<title>Create Your Dream Team</title>'
    . '<link rel="stylesheet" href="./css/createDreamStyle.css">'
    . '</head>'
    . '<body>'
    . '<div class="container">'
    . '<div id="dreamTeam" class="dream-team">'
    . '<div class="position" id="pg">PG</div>'
    . '<div class="position" id="sg">SG</div>'
    . '<div class="position" id="sf">SF</div>'
    . '<div class="position" id="pf">PF</div>'
    . '<div class="position" id="c">C</div>'
    . '</div>'
    // AJAX (Below, a call is made to the js function saveDreamTeam when the user clicks the save button)
    . '<button onclick="saveDreamTeam()">Save</button>'
    . '<br></br>';

    // Then comes the team selection dropdown
    echo '<select id="teamSelect" name="teamSelect" onchange="fetchPlayersForTeam(this.value)">'
    . '<option value="">Select a Team</option>' //Populate the drop down with the team names using ajax. The fetch_players.php is utilised here by the js function.
    . $teamsOptions // $teamsOptions contains all the team options
    . '</select>';
    
    // Player cards container where the AJAX call will populate the players
    echo '<div id="playerCards" class="player-cards"></div>'
    . '</div>' // Closing container div
    . '<script src="./js/froCreateDreamTeam.js"></script>'
    . '</body>'
    . '</html>';
?>
