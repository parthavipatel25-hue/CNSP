<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if (!isset($_GET['id'])) {
    header("Location: /CNSP/admin_notes.php");
    exit();
}

$note_id = intval($_GET['id']);

/* Get file name */
$stmt = $conn->prepare("SELECT file_name FROM notes WHERE id = ?");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$stmt->bind_result($fileName);

if ($stmt->fetch()) {
    $stmt->close();

    /* Delete file */
    $filePath = __DIR__ . "/../uploads/" . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    /* Delete DB record */
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: /CNSP/admin_notes.php?msg=deleted");
exit();