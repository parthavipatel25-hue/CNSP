<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$result = $conn->query("SELECT id, name, email FROM users WHERE role='student'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="css/admin_users.css">
</head>
<body>

<h2>Manage Students</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
      <td>
    <a class="edit" href="edit_student.php?id=<?= $row['id'] ?>">Edit</a>
    <a class="delete" href="backend/delete_student.php?id=<?= $row['id']; ?>"
       onclick="return confirm('Delete this student?')">Delete</a>
</td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
 <a class="back-btn" href="/CNSP/Admin_dashboard.php">⬅ Back to Dashboard</a>

</body>
</html>