<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in or not admin
    exit;
}
?>

<!-- Admin Dashboard Content -->
<h1>Welcome Admin</h1>
<p>Admin content goes here...</p>