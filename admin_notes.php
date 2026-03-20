<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: /CNSP/login.php");
  exit;
}

/* ✅ FILE ICON FUNCTION */
function getFileIcon($filename) {

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    switch($ext) {
        case 'pdf': return '📄';
        case 'doc':
        case 'docx': return '📘';
        case 'ppt':
        case 'pptx': return '📊';
    }
}

$result = $conn->query("
    SELECT n.id, n.file_name, n.status, n.uploaded_at, u.name
    FROM notes n
    JOIN users u ON n.uploaded_by = u.id
    ORDER BY n.uploaded_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Notes</title>
<link rel="stylesheet" href="css/admin_notes.css">
</head>
<body>

<h2>Manage Notes</h2>

<table border="1" cellpadding="8">
<tr>
<th>File</th>
<th>Uploaded By</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<?php
/* ✅ GET ICON */
$icon = getFileIcon($row['file_name']);
?>

<tr>

<td>
<strong><?= $icon ?> <?= htmlspecialchars($row['file_name']) ?></strong>
</td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td><?= htmlspecialchars(strtoupper($row['status'])) ?></td>

<td>
<?php if ($row['status'] === 'pending'): ?>
<a class="approve"
href="backend/approve_notes.php?id=<?= $row['id']; ?>">Approve</a>
<?php endif; ?>

<a class="approve delete"
href="backend/admin_delete_note.php?id=<?= $row['id']; ?>"
onclick="return confirm('Delete this note?')">Delete</a>
</td>

</tr>

<?php endwhile; ?>

</table>

<br>
<a class="back-btn" href="/CNSP/Admin_dashboard.php">⬅ Back to Dashboard</a>

</body>
</html>