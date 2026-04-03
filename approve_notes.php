<?php
session_start();

include __DIR__ . '/db.php';

if (!isset($conn) || !$conn) {
    die("Database connection failed. Please check db.php.");
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /CNSP/login.php");
    exit();
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

if (isset($_GET['action'], $_GET['id'])) {
    $note_id = intval($_GET['id']); 

    $getUser = mysqli_query($conn, "SELECT uploaded_by FROM notes WHERE id=$note_id");
    $rowUser = mysqli_fetch_assoc($getUser);
    $notify_user_id = $rowUser['uploaded_by'];

    if ($_GET['action'] === 'approve') {
        mysqli_query($conn, "UPDATE notes SET status='approved' WHERE id=$note_id");

        $message = "Your note has been APPROVED by admin";
        mysqli_query($conn, "INSERT INTO notifications (user_id, message) 
                             VALUES ('$notify_user_id', '$message')");

    } elseif ($_GET['action'] === 'reject') {
        mysqli_query($conn, "UPDATE notes SET status='rejected' WHERE id=$note_id");

        $message = "Your note has been REJECTED by admin";
        mysqli_query($conn, "INSERT INTO notifications (user_id, message) 
                             VALUES ('$notify_user_id', '$message')");
    }
}

$sql = "SELECT * FROM notes ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching notes: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Approve Notes</title>

<link rel="stylesheet" href="/CNSP/css/approve_notes.css">
</head>
<body>

<h2>Notes Approval</h2>

<table>
<thead>
<tr>
<th>ID</th>
<th>File Name</th>
<th>Uploaded By</th>
<th>Status</th>
<th>Actions</th>
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
<td><?php echo $row['id']; ?></td>

<td>
<strong><?php echo $icon . " " . htmlspecialchars($row['file_name']); ?></strong>
</td>

<td>
<?php
$uploader = '-';
if (!empty($row['uploaded_by'])) {
    $uid = intval($row['uploaded_by']);
    $res = mysqli_query($conn, "SELECT name FROM users WHERE id=$uid");
    if ($res && mysqli_num_rows($res) > 0) {
        $uploader = htmlspecialchars(mysqli_fetch_assoc($res)['name']);
    }
}
echo $uploader;
?>
</td>

<td>
<strong class="<?php echo strtolower($row['status']); ?>">
<?php echo strtoupper($row['status']); ?>
</strong>
</td>

<td>
<a class="view"
href="/CNSP/uploads/<?php echo rawurlencode($row['file_name']); ?>"
target="_blank">View</a>

<a class="approve"
href="?action=approve&id=<?php echo $row['id']; ?>">Approve</a>

<a class="reject"
href="?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="5">No notes found.</td>
</tr>

<?php endif; ?>

</tbody>
</table>

<div style="text-align:center; margin-top:20px;">
<a class="back-btn" href="/CNSP/Admin_dashboard.php"> ← Back to Dashboard</a>
</div>
</body>
</html>