<?php
require('config.php');

$error_message = '';
$success_message = '';

if (isset($_REQUEST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($con, $username);
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($con, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con, $password);
    $reg_date = date("Y-m-d H:i:s");
    
    // Check if the username already exists
    $check_query = "SELECT * FROM `users` WHERE username='$username'";
    $check_result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Username already exists, please choose another username.";
    } else {
        $query = "INSERT into `users` (username, password, email, registration_date) VALUES ('$username', '" . md5($password) . "', '$email', '$reg_date')";
        $result = mysqli_query($con, $query);
        
        if ($result) {
            $success_message = "Registration successful! Please click the link below to login.";
        } else {
            $error_message = "Registration failed, please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container active" id="registerForm">
            <div class="logo">
                <h1>Create Account</h1>
                <p>Join our community</p>
            </div>
            
            <?php if ($success_message): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success_message): ?>
                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" name="username" placeholder=" " required>
                        <label>Username</label>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" name="email" placeholder=" " required>
                        <label>Email Address</label>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    
                    <button type="submit" class="btn">Register</button>
                </form>
            <?php endif; ?>
            
            <div class="switch-form">
                <p>Already have an account?</p>
                <a href="login.php" class="switch-btn">Login now</a>
            </div>
        </div>
    </div>
    
    <script src="assets/script.js"></script>
</body>
</html>