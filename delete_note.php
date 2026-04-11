<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Access denied");
}

if (!isset($_GET['id'])) {
    header("Location: notes_status.php?msg=error");
    exit();
}

$id = intval($_GET['id']);
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT file_name FROM notes WHERE id = ? AND uploaded_by = ?");
$stmt->bind_param("ii", $id, $userId);
$stmt->execute();
$stmt->bind_result($fileName);

if ($stmt->fetch()) {
    $stmt->close();

    $filePath = __DIR__ . "/../uploads/" . $fileName;
    $fileDeleted = false;

    if (file_exists($filePath)) {
        $fileDeleted = unlink($filePath);
    } else {
        $fileDeleted = true; 
    }

    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND uploaded_by = ?");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $dbDeleted = $stmt->affected_rows > 0;
    $stmt->close();

    if ($fileDeleted && $dbDeleted) {
      header("Location: /CNSP/notes_status.php?msg=deleted");
exit();
    } else {
        header("Location: notes_status.php?msg=error");
        exit();
    }

} else {
    $stmt->close();
   header("Location: /CNSP/notes_status.php?msg=deleted");
exit();
}
?>