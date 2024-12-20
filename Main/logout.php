<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

// Redirect back to the homepage after logging out
header('Location: ../index.php');
exit;