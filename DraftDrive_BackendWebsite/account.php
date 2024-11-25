<?php
/*
This is the account.php page. Here the user is displayed the dreamteams that they have created. 
*/

session_start();
include "functions.php";
include "header.php";
include "footer.php";
include "connectionInfo.php";

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$fname = $lname = "";

// Fetch user details from the database
if (isset($_SESSION['username'])) {
    $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);
    if ($conn) {
        $stmt = $conn->prepare("SELECT name, lastName FROM User WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $fname = $row['name'];
                $lname = $row['lastName'];
            }
            $stmt->close();
        }

        $stmt = $conn->prepare("SELECT DreamTeamPlayers.dreamTeamId as dreamId, Player, POS  FROM `DreamTeamMeta` INNER JOIN `DreamTeamPlayers` ON DreamTeamMeta.dreamteamId = DreamTeamPlayers.dreamteamId INNER JOIN Players ON DreamTeamPlayers.playerID = Players.playerId WHERE userId = ?");
        if ($stmt) {
            $stmt->bind_param("s", $_SESSION['userId']);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<div class='player-info'>";
            echo "<table class='player-table'>";
            echo "<tr><th>Team ID</th><th>Player</th><th>Position</th></tr>";
          
            while ($row=$result->fetch_assoc()) {
                
                $dreamTeamId = $row['dreamId'];
                $Player = $row['Player'];
                $POS = $row['POS'];

                echo "<tr><td>$dreamTeamId</td><td>$Player</td><td>$POS</td></tr>";
            }
            $stmt->close();
        }

        $conn->close();
    }
}
?>
<body>
    <br></br>
    <h1>Welcome, <?php echo htmlspecialchars($fname) . " " . htmlspecialchars($lname); ?></h1>
    <!-- Logout Button -->
    <form action="logout.php" method="post">
    <button type="submit" name="logout">Log Out</button>
    </form>
    <br></br>
    <h3>These are the Dream Teams you created!</h3>
</body>