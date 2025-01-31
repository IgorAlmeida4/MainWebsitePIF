<?php
session_start();
include('db.php');
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
</script>