<?php
/*
This is the fetch_playersForCompare.php page. This script is used by the javascript functions in forCompare.php to asynchronusly populate the data for the compare.php
*/

include "connectionInfo.php";
include "functions.php";

//Check if we are getting playerId first. 
if (isset($_GET['playerId'])) {
    $playerId = $_GET['playerId'];
    $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);

    //Selecting all columns from the players table. 
    $stmt = $conn->prepare("SELECT * FROM Players WHERE playerId = ?;");
    $stmt->bind_param("i", $playerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $player = $result->fetch_assoc();
        // Send that data as a json or failure messages.
        echo json_encode(['success' => true, 'player' => $player]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No player found']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No player ID provided']);
}
?>
