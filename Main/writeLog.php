<?php
function writeToLog($nodeId, $action, $message) {
    $logFile = fopen("logs/node_".$nodeId."_log.txt", "a");
    $timestamp = date('Y-m-d H:i:s');
    fwrite($logFile, "[$timestamp] $action: $message\n");
    fclose($logFile);
}
?>