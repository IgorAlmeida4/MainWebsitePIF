<?php

session_start();
global $pdo;
// Check if the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to log in if not admin
    exit;
}

// Fetch all users except the admin user
$stmt = $pdo->prepare("SELECT pk_operator, emailAddress, firstname, lastname FROM Operator WHERE emailAddress != 'admin@example.lu'");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<h1>Admin User Management</h1>
<table>
    <tr>
        <th>Email</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['emailAddress']); ?></td>
            <td><?php echo htmlspecialchars($user['firstname']); ?></td>
            <td><?php echo htmlspecialchars($user['lastname']); ?></td>
            <td>
                <a href="reset-password.php?id=<?php echo $user['pk_operator']; ?>">Reset Password</a> |
                <a href="delete-user.php?id=<?php echo $user['pk_operator']; ?>">Delete User</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
