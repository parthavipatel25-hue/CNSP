
<?php
session_start();
require __DIR__ . '/backend/db.php';

/* allow only student */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Access denied");
}

$userId = $_SESSION['user_id'];

/* ✅ FILE ICON FUNCTION */
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch($ext) {
        case 'pdf': return '📄';
        case 'doc':
        case 'docx': return '📘';
        case 'ppt':
        case 'pptx': return '📊';
        default: return '📁';
    }
}

/* fetch student notes */
$stmt = $conn->prepare(
    "SELECT id, file_name, original_name, status, uploaded_at
     FROM notes 
     WHERE uploaded_by = ?
     ORDER BY uploaded_at DESC"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Uploaded Notes</title>
<link rel="stylesheet" href="css/notes_status.css">
<link rel="stylesheet" href="css/status_badges.css">
</head>

<body>

<h2>My Uploaded Notes</h2>

<?php
/* messages */
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        echo "<p class='success'>Note deleted successfully.</p>";
    }
    if ($_GET['msg'] === 'error') {
        echo "<p class='error'>Failed to delete note.</p>";
    }
}
?>

<?php if ($result && $result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php
/* choose correct file name */
$noteName = !empty($row['original_name']) ? $row['original_name'] : $row['file_name'];

/* ✅ GET ICON */
$icon = getFileIcon($noteName);

/* ✅ GET STATUS CLASS */
$statusClass = '';
switch(strtolower($row['status'])) {
    case 'approved': $statusClass = 'status-approved'; break;
    case 'pending':  $statusClass = 'status-pending';  break;
    case 'rejected': $statusClass = 'status-rejected'; break;
}
?>
<p>
<strong><?= $icon ?> <?= htmlspecialchars($noteName) ?></strong>

<!-- ✅ Colored status badge -->
<span class="status <?= $statusClass ?>">
    <?= ucfirst(htmlspecialchars($row['status'])) ?>
</span>

<?php if ($row['status'] === 'approved'): ?>
| <a href="/CNSP/download_note.php?id=<?= $row['id'] ?>">Download</a>
<?php else: ?>
| <a href="backend/delete_note.php?id=<?= $row['id'] ?>" 
   onclick="return confirm('Are you sure you want to delete this note?');"
   class="btn-delete">Delete</a>
<?php endif; ?>
</p>
<?php endwhile; ?>
<?php else: ?>
<p>No notes uploaded yet.</p>
<?php endif; ?>

<br>
<a class="back-btn" href="/CNSP/Student_dashboard.php">⬅ Back to Dashboard</a>

</body>
</html>