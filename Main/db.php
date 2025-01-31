<?php
$host = 'localhost';
$dbname = 'Plantimeter';
$username = 'root';
$password = '';


// Add to db.php
function handleDatabaseError($e) {
    error_log("Database Error: " . $e->getMessage());
    return "An error occurred. Please try again later.";
}

try {
    // Database operations
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    $error = handleDatabaseError($e);
}

// Use prepared statements for all database queries
function secureQuery($sql, $params) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}