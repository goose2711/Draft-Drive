<?php
/*
This is the fetch_players.php page. This script is used by the javascript functions in forCreateDreamTeam.php to asynchronusly populate the data for the create-dreamteam.php
*/

// To help debug and display errors. 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "functions.php";
include "connectionInfo.php";

$teamCode = isset($_GET['teamCode']) ? $_GET['teamCode'] : '';

// Fetch players from the database based on the selected team code
if (!empty($teamCode)) {
    // Connect to the database
    $conn = connectToDB($dbServer,$dbUser,$dbPass,$dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to obtain player name, postion, id 
    $playerQuery = "SELECT Player, Pos, playerId FROM Players WHERE teamCode = ?";
    $stmt = $conn->prepare($playerQuery);
    $stmt->bind_param("s", $teamCode);
    $stmt->execute();
    $result = $stmt->get_result();

    $players = []; // Intialise the players array. 

    // Strong the player data the players array in json format, easier exchange of data. 
    while ($player = $result->fetch_assoc()) {
        $players[] = [
            'playerID' => $player['playerId'], 
            'Player' => $player['Player'], 
            'Pos' => $player['Pos'], 
        ];
    }

    $stmt->close();
    $conn->close();

    // Send a JSON response back to the AJAX call
    header('Content-Type: application/json');
    echo json_encode($players);
}
?>
