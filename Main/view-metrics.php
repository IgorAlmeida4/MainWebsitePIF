<?php
session_start();
require_once 'db.php';

function getLatestMeasurements($nodeId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM Measurement 
        WHERE fk_node_isRecorded = ? 
        ORDER BY recordDateTime DESC 
        LIMIT 24
    ");
    $stmt->execute([$nodeId]);
    return $stmt->fetchAll();
}

function getNodeInfo($nodeId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT n.*, pv.* 
        FROM Node n 
        LEFT JOIN PlantVariety pv ON n.fk_plantVariety_contains = pv.pk_plantVariety 
        WHERE n.pk_node = ?
    ");
    $stmt->execute([$nodeId]);
    return $stmt->fetch();
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="metrics-container">
    <canvas id="metricsChart"></canvas>
</div>

<script>
    // Add auto-refresh functionality
    setInterval(function() {
        updateMetrics();
    }, 30000); // Updates every 30 seconds

    // Add to metrics.php
    function updateMetrics() {
        setInterval(() => {
            fetch('get-metrics.php')
                .then(response => response.json())
                .then(data => {
                    updateCharts(data);
                });
        }, 30000); // Updates every 30 seconds
    }
</script>>