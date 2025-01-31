<?php
session_start();
include('db.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// User management functions
function addUser($email, $password, $role = 'user') {
    global $pdo;
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Operator (emailAddress, passwordHash, role) VALUES (?, ?, ?)");
        return ['success' => $stmt->execute([$email, $hashedPassword, $role]), 'message' => 'User added successfully'];
    } catch (PDOException $e) {
        error_log("Error adding user: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error adding user'];
    }
}

function updateUser($userId, $email, $role) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE Operator SET emailAddress = ?, role = ? WHERE pk_operator = ?");
        return ['success' => $stmt->execute([$email, $role, $userId]), 'message' => 'User updated successfully'];
    } catch (PDOException $e) {
        error_log("Error updating user: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error updating user'];
    }
}

function deleteUser($userId) {
    global $pdo;
    try {
        // Don't allow deleting your own account
        if ($userId == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'Cannot delete your own account'];
        }
        $stmt = $pdo->prepare("DELETE FROM Operator WHERE pk_operator = ?");
        return ['success' => $stmt->execute([$userId]), 'message' => 'User deleted successfully'];
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error deleting user'];
    }
}

function getAllUsers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT pk_operator, emailAddress, role FROM Operator ORDER BY emailAddress");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        return false;
    }
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = addUser($_POST['email'], $_POST['password'], $_POST['role']);
                $message = $result['message'];
                break;
            case 'update':
                $result = updateUser($_POST['userId'], $_POST['email'], $_POST['role']);
                $message = $result['message'];
                break;
            case 'delete':
                $result = deleteUser($_POST['userId']);
                $message = $result['message'];
                break;
        }
    }
}

// Get all users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Plantimeter System</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Manage Users</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Add User Form -->
    <section class="add-user-form">
        <h2>Add New User</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Add User</button>
        </form>
    </section>

    <!-- Users List -->
    <section class="users-list">
        <h2>Existing Users</h2>
        <?php if ($users && count($users) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['emailAddress']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="userId" value="<?php echo $user['pk_operator']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                            <button onclick="showEditForm(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </section>
</main>

<!-- Edit User Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Edit User</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="userId" id="editUserId">

            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="email" required>

            <label for="editRole">Role:</label>
            <select id="editRole" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Update</button>
            <button type="button" onclick="hideEditForm()">Cancel</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; Igor Almeida 2024-2025 Plants in Focus S.A. All rights reserved.</p>
</footer>

<script>
    function showEditForm(user) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editUserId').value = user.pk_operator;
        document.getElementById('editEmail').value = user.emailAddress;
        document.getElementById('editRole').value = user.role;
    }

    function hideEditForm() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
</body>
</html>