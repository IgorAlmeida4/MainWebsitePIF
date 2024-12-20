<?php
session_start(); // Ensure session is started
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantimeter Admin System</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li>
                <img src="OtherFiles/images/images/logo/250/logo_bg_thin.png" alt="Logo" class="navbar-logo">
            </li>
            <li><a href="index.php">Home</a></li>

            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <!-- If the user is logged in, show Log Out -->
                <li><a href="Main/logout.php">Log Out</a></li>

                    <!-- Conditional Display for Admin or User Dashboard -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <!-- Admin-specific dashboard link -->
                        <li><a href="Main/admin-dashboard.php">Admin Dashboard</a></li>
            <li><a href="Main/metrics.php">Metrics</a></li>
            <li><a href="Main/tasks.php">Tasks</a></li>
                    <?php else: ?>
                        <!-- User-specific dashboard link -->
                        <li><a href="Main/user-dashboard.php">User Dashboard</a></li>
                    <?php endif; ?>


                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <!-- Admin only links -->
                    <li><a href="Main/manage-users.php">Manage Users</a></li>
                <?php endif; ?>
            <?php else: ?>
                <!-- If not logged in, show Log In -->
                <li><a href="Main/login.php">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>Welcome to the Plantimeter System</h1>
    <p>Your platform for managing plants efficiently.</p>
</main>
<br>

<footer>
    <p>&copy; Igor Almeida 2024-2025 Plants in Focus S.A. All rights reserved.</p>
</footer>

<script src="JavaScript/js.js"></script>

</body>
</html>
