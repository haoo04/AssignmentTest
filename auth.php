<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Optional: Check if session has expired (e.g. 2 hours)
$session_timeout = 2 * 60 * 60; // 2 hours, in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Session has expired
    session_unset();
    session_destroy();
    header("Location: login.php?message=session_expired");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>