<?php
/* 
This is the save_dreamteam.php. Here, this script is used to used asynchronusly by the js script forCreateDreamTeam, specifically by 
the sendDreamTeamData() function. When the user drops all their desired players into the dreamTeam 
placeholders and click save, this script helps insert those players into the dreamTeam Table in the database. 
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "connectionInfo.php";
include "functions.php";

// Getting the userID from (technically process-login when the data is being verfied, the session super global carries the userID.)
session_start();
 if (isset($_SESSION['userId'])) {
      $userId = $_SESSION['userId'];
      echo  $userId;
 } 

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$dreamTeam = $data['dreamTeam'] ?? [];

$conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);

// Begin transaction
$conn->begin_transaction();

try {
    // Insert a new record into DreamTeamMeta and get the ID
    $metaQuery = $conn->prepare("INSERT INTO DreamTeamMeta (userId, creationDate, isPrivate) VALUES (?, CURDATE(), 0)");
    $metaQuery->bind_param("i", $userId);
    $metaQuery->execute();
    $dreamTeamId = $conn->insert_id;

    // Insert each player into DreamTeamPlayers
    foreach ($dreamTeam as $playerId) {
        $teamQuery = $conn->prepare("INSERT INTO DreamTeamPlayers (dreamteamId, playerID) VALUES (?, ?)");
        $teamQuery->bind_param("ii", $dreamTeamId, $playerId);
        $teamQuery->execute();
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Dream Team saved successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error saving Dream Team: ' . $e->getMessage()]);
}
exit;
$conn->close();


?>

