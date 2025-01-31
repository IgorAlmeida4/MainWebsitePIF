<?php
session_start();
include('db.php');

function assignNode($nodeId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Node SET fk_operator_belongs = ? WHERE pk_node = ?");
    return $stmt->execute([$userId, $nodeId]);
}

function deleteNode($nodeId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Node WHERE pk_node = ?");
    return $stmt->execute([$nodeId]);
}
?>