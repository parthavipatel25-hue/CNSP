<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* 🔹 Mark all notifications as READ */
mysqli_query($conn, 
"UPDATE notifications 
 SET status='read' 
 WHERE user_id='$user_id'");

/* 🔹 Fetch notifications */
$result = mysqli_query($conn, 
"SELECT * FROM notifications 
 WHERE user_id='$user_id' 
 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications</title>

<style>
body {
    font-family: Arial;
    background: #f5f7ff;
}

.container {
    width: 600px;
    margin: 40px auto;
}

h2 {
    text-align: center;
}

/* Notification Box */
.note {
    background: white;
    padding: 15px;
    margin: 12px 0;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.note small {
    color: gray;
}

/* Back Button */
.back-btn {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: white;
    background: #6366F1;
    padding: 10px 15px;
    border-radius: 6px;
}
</style>

</head>

<body>

<div class="container">

<h2>🔔 Notifications</h2>

<?php if(mysqli_num_rows($result) > 0): ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>

        <div class="note">
            <strong><?php echo htmlspecialchars($row['message']); ?></strong>
            <br>
            <small><?php echo $row['created_at']; ?></small>
        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p style="text-align:center;">No notifications yet.</p>

<?php endif; ?>

<br>

<a href="student_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</div>

</body>
</html>