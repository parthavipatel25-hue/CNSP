<?php
session_start();
require __DIR__ . '/backend/db.php';

/* Restrict page to admin only */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

/* Fetch activity logs */
$sql = "SELECT a.*, u.name 
        FROM activity_logs a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.log_time DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Activity Logs</title>

<link rel="stylesheet" href="css/view_logs.css">

</head>

<body>

<!-- ✅ TOP LEFT BACK BUTTON -->
<div style="margin: 20px;">
    <a class="back-btn" href="/CNSP/Admin_dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2 style="text-align:center;">Activity Logs</h2>

<table border="1" cellpadding="10" cellspacing="0">

<tr>
<th>User</th>
<th>Action</th>
<th>Time</th>
</tr>

<?php if ($result && mysqli_num_rows($result) > 0): ?>

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo htmlspecialchars($row['name']); ?></td>

<td><?php echo htmlspecialchars($row['action']); ?></td>

<td><?php echo htmlspecialchars($row['log_time']); ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="3">No activity logs found.</td>
</tr>

<?php endif; ?>

</table>

</body>
</html>