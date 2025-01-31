<?php
session_start();
include('db.php');
global $pdo;

// Move functions outside of the POST handling
function changePassword($userId, $newPassword) {
    global $pdo;
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE Operator SET passwordHash = ? WHERE pk_operator = ?");
    return $stmt->execute([$hashedPassword, $userId]);
}

function checkAuth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function updateSessionTimeout() {
    if (isset($_SESSION['LAST_ACTIVITY']) &&
        (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Get the user data from the database
    $stmt = $pdo->prepare("SELECT pk_operator, emailAddress, passwordHash FROM Operator WHERE emailAddress = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['passwordHash'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['pk_operator'];

        // Check if the logged-in user is an admin
        if ($username === 'admin@example.com' || $username === 'testuser@example.com') {
            $_SESSION['role'] = 'admin';
        } else {
            $_SESSION['role'] = 'user';
        }

        $_SESSION['LAST_ACTIVITY'] = time(); // Initialize session timeout
        header('Location: ../index.php');
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plantimeter System</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Login</h1>
    <form action="login.php" method="POST" class="login-form">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

        <?php if (isset($error)) {
            echo "<p class='error'>$error</p>";
        } ?>
    </form>

    <br>
    <p>Don't have an account? Contact your administrator ("Igor (-:") to register.</p>
</main>

<footer>
    <p>&copy; Igor Almeida 2024-2025 Plants in Focus S.A. All rights reserved.</p>
</footer>

</body>
</html>