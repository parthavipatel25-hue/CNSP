<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];
$note_id = $_POST['note_id'];

$check = mysqli_query($conn,
"SELECT * FROM favorites WHERE user_id=$user_id AND note_id=$note_id");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn,
    "DELETE FROM favorites WHERE user_id=$user_id AND note_id=$note_id");
} else {
    mysqli_query($conn,
    "INSERT INTO favorites (user_id, note_id) VALUES ($user_id, $note_id)");
}

header("Location: all_notes.php");
exit();
?>