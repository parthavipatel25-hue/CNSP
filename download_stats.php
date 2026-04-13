<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/backend/db.php';

/* allow only admin */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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
?>

<!DOCTYPE html>
<html>
<head>
<title>Download Statistics</title>
<link rel="stylesheet" href="css/download_stats.css">
</head>

<body>

<div class="dashboard">

<h2>Download Statistics</h2>

<?php
/* TOTAL DOWNLOADS */

$total = mysqli_fetch_assoc(
mysqli_query($conn,"SELECT COUNT(*) AS total FROM downloads")
)['total'];

echo "<h3>Total Downloads: $total</h3>";
?>

<hr>

<h3>Today's Downloads</h3>

<?php

$today = mysqli_query($conn,"
SELECT n.file_name, d.downloaded_at
FROM downloads d
JOIN notes n ON d.note_id = n.id
WHERE DATE(d.downloaded_at)=CURDATE()
ORDER BY d.downloaded_at DESC
");

if(mysqli_num_rows($today)==0){

echo "<p>No downloads today.</p>";

}else{

while($row=mysqli_fetch_assoc($today)){

$file = htmlspecialchars($row['file_name']);
$time = htmlspecialchars($row['downloaded_at']);

/* ✅ ICON */
$icon = getFileIcon($row['file_name']);

echo "<div class='download-item'>";
echo "<b>$icon $file</b><br>";
echo "<small>Downloaded at: $time</small>";
echo "</div>";

}

}

?>

<hr>

<h3>Most Downloaded Notes</h3>

<?php

$popular = mysqli_query($conn,"
SELECT n.file_name, COUNT(d.id) AS total
FROM downloads d
JOIN notes n ON d.note_id = n.id
GROUP BY d.note_id
ORDER BY total DESC
LIMIT 5
");

if(mysqli_num_rows($popular)==0){

echo "<p>No downloads yet.</p>";

}else{

while($row=mysqli_fetch_assoc($popular)){

$file = htmlspecialchars($row['file_name']);
$count = htmlspecialchars($row['total']);

/* ✅ ICON */
$icon = getFileIcon($row['file_name']);

echo "<div class='download-item'>";
echo "<b>$icon $file</b> - $count downloads";
echo "</div>";

}

}

?>

<br><br>

<a href="Admin_dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>