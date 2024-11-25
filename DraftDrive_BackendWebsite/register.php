<?php

/* 
This is the register.php. In this script, a user can create a new account and their details are stored in the database. 
*/

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "functions.php"; 
include "header.php";
include "connectionInfo.php"; 

$message = ""; // Variable to store messages for the user

$fname = $lname = $username = $email = $password = $confirm_password = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        // Obtain and process user input
        $fname = !empty($_POST["fname"]) ? trim($_POST["fname"]) : "";
        $lname = !empty($_POST["lname"]) ? trim($_POST["lname"]) : "";
        $username = !empty($_POST["username"]) ? trim($_POST["username"]) : "";
        $email = !empty($_POST["email"]) ? trim($_POST["email"]) : "";
        $password = !empty($_POST["password"]) ? $_POST["password"] : "";
        $confirm_password = !empty($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";
        
        // Validate all the fields.
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $message = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $message = "Passwords do not match.";
        } else {
            // Database connection
            $conn = connectToDB($dbServer, $dbUser, $dbPass, $dbName);
            if ($conn) {
                // Check if the username is already taken
                $stmt = $conn->prepare("SELECT userId FROM User WHERE username = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows > 0) {
                        $message = "Username already taken.";
                    } else {
                        // Password hashing for best security practise. 
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
                        // Database insertion
                        $stmt = $conn->prepare("INSERT INTO User (username, email, name, lastName, password) VALUES (?, ?, ?, ?, ?)");
                        if ($stmt) {
                            $stmt->bind_param("sssss", $username, $email, $fname, $lname, $hashed_password);
                            // Execute and check for errors
                            if ($stmt->execute()) {
                                $message = "Registration successful.";
                                
                                header("Location: account.php");
                                exit; // Ensure no further execution of the script
                            } else {
                                $message = "Registration failed: " . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $message = "Error preparing statement: " . $conn->error;
                        }
                    }
                } else {
                    $message = "Error preparing statement: " . $conn->error;
                }
                $conn->close();
            } else {
                $message = "Database connection failed.";
            }
        }
    }
} else{
    $username = "";
    $email = "";
    $password = "";
    $confirm_password = "";
    $fname = "";
    $lname = "";
}
?>
<!-- This below is the html section that shows all the front end fields -->
<body>
    <div class="register-container">
        <form action="register.php" method="post">
            <h2>Create a New Account</h2>

            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required value="<?php echo htmlspecialchars($fname); ?>">

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required value="<?php echo htmlspecialchars($lname); ?>">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" name="submit">Register</button>
            <?php if (!empty($message)) echo '<p>' . htmlspecialchars($message) . '</p>'; ?>
        </form>
    </div>
</body>
