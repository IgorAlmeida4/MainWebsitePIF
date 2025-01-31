<?php
global $pdo;
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Get nodes function
function getNodes($operatorId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT n.*, pv.commonName, pv.botanicalName 
            FROM Node n 
            LEFT JOIN PlantVariety pv ON n.fk_plantVariety_contains = pv.pk_plantVariety 
            WHERE n.fk_operator_belongs = ?
        ");
        $stmt->execute([$operatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching nodes: " . $e->getMessage());
        return false;
    }
}

// Add node function with validation
function addNode($macAddress, $name, $plantVarietyId, $operatorId) {
    global $pdo;

    // Validate MAC address format
    if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $macAddress)) {
        return ['success' => false, 'message' => 'Invalid MAC address format'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO Node (macAddress, name, fk_plantVariety_contains, fk_operator_belongs, plantingDate) 
            VALUES (?, ?, ?, ?, CURRENT_DATE)
        ");
        $result = $stmt->execute([$macAddress, $name, $plantVarietyId, $operatorId]);
        return ['success' => $result, 'message' => $result ? 'Node added successfully' : 'Failed to add node'];
    } catch (PDOException $e) {
        error_log("Error adding node: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = addNode(
                    $_POST['macAddress'],
                    $_POST['name'],
                    $_POST['plantVarietyId'],
                    $_SESSION['user_id']
                );
                $message = $result['message'];
                break;
        }
    }
}

// Get all nodes for the current user
$nodes = getNodes($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Nodes - Plantimeter System</title>
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
    <h1>Manage Nodes</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Add Node Form -->
    <section class="add-node-form">
        <h2>Add New Node</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">

            <label for="macAddress">MAC Address:</label>
            <input type="text" id="macAddress" name="macAddress" required
                   pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$"
                   placeholder="00:11:22:33:44:55">

            <label for="name">Node Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="plantVarietyId">Plant Variety:</label>
            <select id="plantVarietyId" name="plantVarietyId" required>
                <?php
                // Fetch and display plant varieties
                $stmt = $pdo->query("SELECT pk_plantVariety, commonName FROM PlantVariety");
                while ($row = $stmt->fetch()) {
                    echo "<option value='" . htmlspecialchars($row['pk_plantVariety']) . "'>"
                        . htmlspecialchars($row['commonName']) . "</option>";
                }
                ?>
            </select>

            <button type="submit">Add Node</button>
        </form>
    </section>

    <!-- Display Existing Nodes -->
    <section class="nodes-list">
        <h2>Your Nodes</h2>
        <?php if ($nodes && count($nodes) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>MAC Address</th>
                    <th>Plant</th>
                    <th>Planting Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($nodes as $node): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($node['name']); ?></td>
                        <td><?php echo htmlspecialchars($node['macAddress']); ?></td>
                        <td><?php echo htmlspecialchars($node['commonName']); ?></td>
                        <td><?php echo htmlspecialchars($node['plantingDate']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No nodes found. Add your first node above.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; Igor Almeida 2024-2025 Plants in Focus S.A. All rights reserved.</p>
</footer>
</body>
</html>