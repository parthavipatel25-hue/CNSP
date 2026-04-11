<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = intval($_GET['id']);
$conn->query("DELETE FROM users WHERE id = $id");

$admin_id = $_SESSION['user_id'];

mysqli_query($conn,
"INSERT INTO activity_logs (user_id, action)
VALUES ($admin_id, 'Deleted a student')");

header("Location: ../admin_users.php");