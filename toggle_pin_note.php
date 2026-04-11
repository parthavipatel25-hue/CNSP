<?php
session_start();
require 'db.php'; // your database connection

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    echo json_encode(['success' => false, 'message' => 'Not logged in or not student']);
    exit;
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    echo json_encode(['success' => false, 'message' => 'Note ID missing']);
    exit;
}

$note_id = intval($_GET['id']); // make sure ID is integer

// Check if note exists
$stmt = $conn->prepare("SELECT is_pinned FROM notes WHERE id=?");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if(!$result){
    echo json_encode(['success' => false, 'message' => 'Note not found in database']);
    exit;
}

$new_status = $result['is_pinned'] ? 0 : 1;

// Update pin status
$stmt = $conn->prepare("UPDATE notes SET is_pinned=? WHERE id=?");
$stmt->bind_param("ii", $new_status, $note_id);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Pin status updated']);
exit;
?>