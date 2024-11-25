<?php
/* 
This is the process-login.php page. When a user clicks submit after entering their username and password, this page 
gets that data and processes (validate) it. 
*/
session_start();
include "functions.php";
include "connectionInfo.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    if (empty($username) || empty($password)) {
        $message = "Both username and password are required.";
    } else {
        $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);
        if ($conn) {
            $stmt = $conn->prepare("SELECT userId, password FROM User WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    //We store the password as a hash, this is to encrypt it. 
                    $hashed_password = $row['password'];
                    if (password_verify($password, $hashed_password)) {
                        $userId = $row['userId'];
                            $_SESSION['userId'] = $userId;
                            $_SESSION['logged_in'] = true; // Set the session variable
                            $_SESSION['username'] = $username; // Optionally store the username
                            $message = "Login successful.";
                            $_SESSION['message'] = $message;
                            header("Location: account.php"); // Redirect on successful login
                            exit; // Stop the script after redirection
                        //}
                    } else {
                        $message = "Invalid password.";
                    }
                } else {
                    $message = "Username does not exist.";
                }
                $stmt->close();
            } else {
                $message = "Error preparing statement: " . $conn->error;
            }
            $conn->close();
        } else {
            $message = "Database connection failed.";
        }
    }
    $_SESSION['message'] = $message;
    header("Location: login.php"); // Redirect back to login page
    exit; // Stop the script after redirection
}

?>
