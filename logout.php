<?php
session_start();
require __DIR__ . '/backend/db.php';

/* log logout activity BEFORE destroying session */
if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    mysqli_query($conn,
    "INSERT INTO activity_logs (user_id, action)
     VALUES ($user_id, 'Logged out')");
}

/* destroy session */
session_destroy();

/* redirect to login */
header("Location: login.php");
exit;