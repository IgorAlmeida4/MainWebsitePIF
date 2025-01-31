<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'];
    $userId = $_SESSION['user_id'];

    if(changePassword($userId, $newPassword)) {
        $message = "Password updated successfully";
    }
}
?>