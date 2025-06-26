<?php
include("auth.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Center - Home</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container active" id="homePage">
            <div class="home-container">
                <h1 class="welcome-title">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
                <p class="welcome-subtitle">Welcome to the user center</p>
                
                <div class="nav-links">
                    <a href="views/discussions/all_posts.php" class="nav-link">
                        View all posts
                    </a>
                    <a href="profile.php" class="nav-link">
                        Personal information
                    </a>
                    <a href="settings.php" class="nav-link">
                        Account settings
                    </a>
                    <a href="logout.php" class="nav-link logout">
                        Log out
                    </a>
                </div>
                
                <div class="user-info">
                    <p class="info-text">User ID: <?php echo $_SESSION['user_id']; ?></p>
                    <p class="info-text">Login time: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/script.js"></script>
</body>
</html>