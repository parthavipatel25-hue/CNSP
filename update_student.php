<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = intval($_POST['id']);
$name = $_POST['name'];
$email = $_POST['email'];

$stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
$stmt->bind_param("ssi", $name, $email, $id);
$stmt->execute();

header("Location: ../admin_users.php");