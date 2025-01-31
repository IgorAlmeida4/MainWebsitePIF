<?php
require_once 'db.php';

function checkThresholds() {
    global $pdo;

    // Get latest measurements for all nodes with their plant variety thresholds
    $query = "
        SELECT 
            n.pk_node,
            m.soilMoisture,
            m.ambientBrightness,
            pv.soilMoistureThreshold,
            pv.ambientBrightnessThreshold
        FROM Node n
        JOIN PlantVariety pv ON n.fk_plantVariety_contains = pv.pk_plantVariety
        JOIN Measurement m ON m.fk_node_isRecorded = n.pk_node
        WHERE m.recordDateTime = (
            SELECT MAX(recordDateTime)
            FROM Measurement
            WHERE fk_node_isRecorded = n.pk_node
        )";

    try {
        $stmt = $pdo->query($query);
        $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($nodes as $node) {
            // Check soil moisture
            if($node['soilMoisture'] < $node['soilMoistureThreshold']) {
                createTask($node['pk_node'], 'pump', 100);
            }

            // Check ambient brightness
            if($node['ambientBrightness'] < $node['ambientBrightnessThreshold']) {
                createTask($node['pk_node'], 'light', 100);
            }
        }

        echo "Automation check completed successfully\n";

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

function createTask($nodeId, $taskType, $setPoint) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO Task (fk_node_isAssigned, taskType, setPoint) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$nodeId, $taskType, $setPoint]);
        echo "Created $taskType task for node $nodeId\n";
        return true;
    } catch(PDOException $e) {
        echo "Error creating task: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run automation
checkThresholds();
