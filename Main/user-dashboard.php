<?php
session_start();

// Check if the user is logged in and is a regular user
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'user') {
    header('Location: login.php'); // Redirect to log in if not logged in or not a user
    exit;
}
?>

<!-- User Dashboard Content -->
<h1>Welcome User</h1>
<p>User content goes here...</p>

