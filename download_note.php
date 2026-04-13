<?php
session_start();
require __DIR__ . '/backend/db.php';

/* check login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

/* check note id */
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$note_id = intval($_GET['id']);

/* get note file */
$sql = "SELECT file_name FROM notes WHERE id = $note_id AND status = 'approved'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("File not found");
}

$row = mysqli_fetch_assoc($result);
$file = $row['file_name'];

$file_path = "uploads/" . $file;

/* check if file exists */
if (!file_exists($file_path)) {
    die("File missing on server");
}

/* record download ONLY for students */
if ($role === 'student') {

    mysqli_query($conn, "
        INSERT INTO downloads (note_id, user_id, downloaded_at)
        VALUES ($note_id, $user_id, NOW())
    ");

}

/* force download */
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($file_path));

readfile($file_path);
exit;
?>