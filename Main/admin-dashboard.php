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

// Add new admin functionalities
<div class="admin-controls">
    <section class="user-management">
        <h2>User Management</h2>
        <a href="manage-users.php">Manage Users</a>
    </section>

    <section class="node-management">
        <h2>Node Management</h2>
        <a href="manage-nodes.php">Manage Nodes</a>
    </section>

    <section class="plant-management">
        <h2>Plant Species Management</h2>
        <a href="manage-plants.php">Manage Plants</a>
    </section>
</div>
