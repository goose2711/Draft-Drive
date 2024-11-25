<?php
/* 
This is login.php. This script is used to firstly display a front end component to the user
that has username and password fields. The user entered data is then sent to process-login 
where the data is processed and returned. 
*/
    session_start();
    include "header.php";
    $message = "";
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']); 
    }
?>
<body>
    <div class="login-container">
        <form action="process-login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
            <div><?php echo htmlspecialchars($message); ?></div>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
            </div>
        </form>
    </div>
</body>
