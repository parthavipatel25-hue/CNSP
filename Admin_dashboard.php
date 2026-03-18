<?php
session_start();
require __DIR__ . '/backend/db.php';

/* Restrict page to admin */
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

/* SUMMARY COUNTS */
$total_students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='student'"))['total'];
$total_notes = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM notes"))['total'];
$pending_notes = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM notes WHERE status='pending'"))['total'];
$total_downloads = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM downloads"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>

<div class="dashboard">

<header class="dash-header">
<h1>Admin Dashboard</h1>

<form action="logout.php" method="POST">
<button type="submit" class="logout-btn">Logout</button>
</form>
</header>

<!-- SUMMARY CARDS -->
<div class="summary-cards">

<div class="card-box">
<h3>Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>

<div class="card-box">
<h3>Total Notes</h3>
<p><?php echo $total_notes; ?></p>
</div>

<div class="card-box">
<h3>Pending Approvals</h3>
<p><?php echo $pending_notes; ?></p>
</div>

<div class="card-box">
<h3>Total Downloads</h3>
<p><?php echo $total_downloads; ?></p>
</div>

</div>

<!-- MAIN DASHBOARD CARDS -->
<div class="cards">
<a href="/CNSP/backend/approve_notes.php" class="card">Pending Notes</a>
<a href="/CNSP/view_notes.php" class="card">View Notes</a>
<a href="/CNSP/admin_users.php" class="card">Manage Students</a>
<a href="/CNSP/admin_notes.php" class="card">Manage Notes</a>
<a href="/CNSP/profile.php" class="card">Profile</a>
<a href="/CNSP/view_logs.php" class="card">Activity Logs</a>
<a href="/CNSP/download_stats.php" class="card">Downloads</a>
</div>

<!-- RECENT UPLOADS -->
<div class="recent-section">
<h2>Recently Uploaded Files</h2>

<?php
$stmt = $conn->prepare(
"SELECT n.file_name, n.uploaded_at, u.name AS uploader_name
 FROM notes n
 JOIN users u ON n.uploaded_by = u.id
 ORDER BY n.uploaded_at DESC
 LIMIT 5"
);

$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
echo "<p>No recent uploads.</p>";
}else{
while($row = $result->fetch_assoc()){

$file_name = htmlspecialchars($row['file_name']);
$uploaded_at = htmlspecialchars($row['uploaded_at']);
$uploader = htmlspecialchars($row['uploader_name']);
$icon = getFileIcon($row['file_name']);

echo "<div class='recent-item'>";
echo "<strong>$icon $file_name</strong><br>";
echo "<small>Uploaded on: $uploaded_at | By: $uploader</small>";
echo "</div>";
}
}
?>
</div>

<!-- ACTIVITY LOGS -->
<div class="recent-section">
<h2>Recent Activity Logs</h2>

<?php
$logs = mysqli_query($conn,"
SELECT a.*, u.name
FROM activity_logs a
JOIN users u ON a.user_id = u.id
ORDER BY a.log_time DESC
LIMIT 5
");

if(mysqli_num_rows($logs)==0){
echo "<p>No activity yet.</p>";
}else{
while($row=mysqli_fetch_assoc($logs)){
$user = htmlspecialchars($row['name']);
$action = htmlspecialchars($row['action']);
$time = htmlspecialchars($row['log_time']);

echo "<div class='recent-item'>";
echo "<strong>$user</strong> - $action <br>";
echo "<small>$time</small>";
echo "</div>";
}
}
?>
</div>

<!-- DOWNLOAD STATISTICS -->
<div class="recent-section">
<h2>Download Statistics</h2>

<h3>Today's Downloads</h3>

<?php
$today_downloads = mysqli_query($conn,"
SELECT n.file_name, d.downloaded_at
FROM downloads d
JOIN notes n ON d.note_id = n.id
WHERE DATE(d.downloaded_at) = CURDATE()
ORDER BY d.downloaded_at DESC
LIMIT 5
");

if(mysqli_num_rows($today_downloads)==0){
echo "<p>No downloads today.</p>";
}else{
while($row=mysqli_fetch_assoc($today_downloads)){
$file = htmlspecialchars($row['file_name']);
$time = htmlspecialchars($row['downloaded_at']);
$icon = getFileIcon($row['file_name']);

echo "<div class='recent-item'>";
echo "<strong>$icon $file</strong><br>";
echo "<small>Downloaded at: $time</small>";
echo "</div>";
}
}
?>

<h3>Most Downloaded Notes</h3>

<?php
$popular = mysqli_query($conn,"
SELECT n.file_name, COUNT(d.id) AS total_downloads
FROM downloads d
JOIN notes n ON d.note_id = n.id
GROUP BY d.note_id
ORDER BY total_downloads DESC
LIMIT 5
");

if(mysqli_num_rows($popular)==0){
echo "<p>No download data.</p>";
}else{
while($row=mysqli_fetch_assoc($popular)){
$file = htmlspecialchars($row['file_name']);
$count = htmlspecialchars($row['total_downloads']);
$icon = getFileIcon($row['file_name']);

echo "<div class='recent-item'>";
echo "<strong>$icon $file</strong> - $count downloads";
echo "</div>";
}
}
?>
</div>

</div>


</body>
</html>