<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

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

if ($role === 'admin') {
    $sql = "SELECT n.*, u.name AS uploader_name 
            FROM notes n 
            LEFT JOIN users u ON n.uploaded_by = u.id 
            ORDER BY n.id DESC";
} else {
    $sql = "SELECT n.*, u.name AS uploader_name 
            FROM notes n 
            LEFT JOIN users u ON n.uploaded_by = u.id 
            WHERE uploaded_by = $user_id 
            ORDER BY n.id DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Notes</title>
<link rel="stylesheet" href="css/view_notes.css">
</head>

<body>

<div class="container">
<h2>Notes</h2>

<table class="table-card">
<thead>
<tr>
<th>File</th>
<th>Uploaded By</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>

<?php
/* ✅ GET ICON */
$icon = getFileIcon($row['file_name']);
?>

<tr>

<td>
<strong><?= $icon ?> <?= htmlspecialchars($row['file_name']) ?></strong>
</td>

<td><?= htmlspecialchars($row['uploader_name'] ?? '-') ?></td>

<td>
<span class="status <?= strtolower($row['status']) ?>">
<?= ucfirst($row['status']) ?>
</span>
</td>

<td>
<?php if ($row['status'] === 'approved' && !empty($row['file_name'])): ?>
<a class="view-btn"
href="/CNSP/uploads/<?php echo rawurlencode($row['file_name']); ?>"
target="_blank">
View / Download
</a>
<?php else: ?>
<span class="not-available">Not available</span>
<?php endif; ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="4" style="text-align:center;">No notes found.</td>
</tr>

<?php endif; ?>

</tbody>
</table>

<br>
<a class="back-btn" href="/CNSP/Admin_dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>