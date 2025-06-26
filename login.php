<?php
session_start();
require('config.php');

$error_message = '';
$success_message = '';

if (isset($_POST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($con, $username);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($con, $password);
    
    $query = "SELECT * FROM `users` WHERE username='$username' AND password='" . md5($password) . "'";
    $result = mysqli_query($con, $query) or die(mysqli_error($con));
    $rows = mysqli_num_rows($result);
    
    if ($rows == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Username or password is incorrect, please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container active" id="loginForm">
            <div class="logo">
                <h1>Welcome back</h1>
                <p>Login to your account</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder=" " required>
                    <label>Username</label>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" placeholder=" " required>
                    <label>Password</label>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            
            <div class="switch-form">
                <p>No account yet?</p>
                <a href="registration.php" class="switch-btn">Register now</a>
            </div>
        </div>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>