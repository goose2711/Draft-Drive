<?php
/* IAT352 FInal Project: Draft Drive 
By: Agastya Oruganti, Elina Mokhammad, Baolati Pazeli
Instructor:  Rafael Arias Gonzalez
TA: Reyhaneh Ahmadi Nokabadi 
*/
// index.php file; The main file that is used to start the website (home).

    include "functions.php";
    include "header.php";
    include "connectionInfo.php";
    echo '<script src="./js/forIndexPage.js"></script>';

    $conn = connectToDB($dbServer,$dbUser,$dbPass,$dbName); // Start a connection to the database. 
    deployTeamToggle($conn); // Use function dePloyTeamToggle to display the list of teams. Toggle down for list of players in the team.
      
    $conn->close(); // Close the connection to the database.

?>