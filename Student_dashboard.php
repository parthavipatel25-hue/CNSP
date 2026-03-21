
<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /CNSP/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* 🔔 Get notification count */
$countQuery = mysqli_query($conn, 
"SELECT COUNT(*) AS total FROM notifications 
 WHERE user_id='$user_id' AND status='unread'");

$count = mysqli_fetch_assoc($countQuery)['total'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="css/dashboard.css">

<style>
/* 🔔 Notification Icon */
.notification-icon {
    position: relative;
    font-size: 22px;
    text-decoration: none;
    margin-right: 20px;
    color: black;
}

/* 🔴 Badge */
.badge {
    position: absolute;
    top: -6px;
    right: -10px;
    background: red;
    color: white;
    font-size: 12px;
    padding: 3px 7px;
    border-radius: 50%;
}

/* 📁 Recent Files */
.recent-item {
    background: #f1f5f9;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 6px;
}

.file-type {
    color: #38bdf8;
    font-weight: bold;
}
</style>

</head>
<body>

<div class="dashboard">

<header class="dash-header">

<h1>Student Dashboard</h1>

<div style="display:flex; align-items:center; gap:15px;">

    <!-- 🔔 Notification Bell -->
    <a href="notifications.php" class="notification-icon">
        🔔
        <?php if($count > 0): ?>
            <span class="badge"><?php echo $count; ?></span>
        <?php endif; ?>
    </a>

    <!-- Logout -->
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-btn">Logout</button>
    </form>

</div>

</header>

<div class="cards">
<a href="/CNSP/upload_notes.php" class="card">Upload Notes</a>
<a href="/CNSP/notes_status.php" class="card">Notes Status</a>
<a href="/CNSP/profile.php" class="card">Profile</a>
<a href="/CNSP/all_notes.php" class="card">All Notes</a>
</div>

<div class="recent-section">
<h2>Recently Uploaded Files</h2>

<?php
$stmt = $conn->prepare(
"SELECT file_name, uploaded_at
 FROM notes
 WHERE uploaded_by = ?
 ORDER BY uploaded_at DESC
 LIMIT 5"
);

$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo "<p>No recent uploads.</p>";
}else{

    while($row = $result->fetch_assoc()){

        $file_name = htmlspecialchars($row['file_name']);
        $uploaded_at = htmlspecialchars($row['uploaded_at']);
        $file_extension = strtoupper(pathinfo($file_name, PATHINFO_EXTENSION));

        /* ✅ GET ICON */
        $icon = getFileIcon($file_name);

        echo "<div class='recent-item'>";
        echo "<strong>{$icon} {$file_name}</strong> <span class='file-type'>({$file_extension})</span><br>";
        echo "<small>Uploaded on: {$uploaded_at}</small>";
        echo "</div>";
    }
}
?>

</div>

</div>

</body>
</html>