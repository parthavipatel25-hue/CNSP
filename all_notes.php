<?php
session_start();
require __DIR__ . '/backend/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Access denied");
}

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$fav  = $_GET['fav'] ?? '';

$user_id = $_SESSION['user_id'];


$sql = "SELECT n.*, u.name AS uploader_name,
        IF(f.note_id IS NULL, 0, 1) AS is_favorite
        FROM notes n
        JOIN users u ON n.uploaded_by = u.id
        LEFT JOIN favorites f 
        ON n.id = f.note_id AND f.user_id = $user_id
        WHERE n.status='approved'
        AND NOT EXISTS (
            SELECT 1 FROM notes n2
            WHERE n2.original_name = n.original_name
            AND n2.uploaded_by = n.uploaded_by
            AND n2.version > n.version
            AND n2.status='approved'
        )";

if ($search != '') {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $sql .= " AND n.file_name LIKE '%$searchEscaped%'";
}


if ($type != '') {
    $typeEscaped = mysqli_real_escape_string($conn, $type);
    $sql .= " AND n.note_type='$typeEscaped'";
}

if ($fav == '1') {
    $sql .= " AND n.id IN (
        SELECT note_id FROM favorites WHERE user_id = $user_id
    )";
}

$sql .= " ORDER BY n.is_pinned DESC, n.uploaded_at DESC";

$result = mysqli_query($conn, $sql);


function highlightText($text, $search) {
    if (empty($search)) return htmlspecialchars($text);
    return preg_replace(
        "/(" . preg_quote($search, '/') . ")/i",
        "<span class='highlight'>$1</span>",
        htmlspecialchars($text)
    );
}

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
?>

<!DOCTYPE html>
<html>
<head>
<title>All Notes</title>
<link rel="stylesheet" href="css/view_notes.css">
<link rel="stylesheet" href="css/status_badges.css?v=1">

<style>
.highlight {
    background-color: #fff176;
    font-weight: bold;
    padding: 2px 4px;
    border-radius: 4px;
}

.fav-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}


.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
}
</style>
</head>

<body>
<div class="container">
<h2>Available Notes</h2>

<form method="GET" class="search-filter">
<input type="text" name="search" placeholder="Search notes..." value="<?php echo htmlspecialchars($search); ?>">

<select name="type">
<option value="">All Types</option>
<option value="PDF" <?php if($type=='PDF') echo 'selected'; ?>>PDF</option>
<option value="DOC" <?php if($type=='DOC') echo 'selected'; ?>>DOC</option>
<option value="PPT" <?php if($type=='PPT') echo 'selected'; ?>>PPT</option>
</select>

<select name="fav">
<option value="">All</option>
<option value="1" <?php if($fav=='1') echo 'selected'; ?>>⭐ Favorites</option>
</select>

<button type="submit">Search</button>
</form>

<table class="table-card">
<tr>
<th>File Name</th>
<th>Uploaded By</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td>
<?php
$icon = getFileIcon($row['file_name']);
echo $icon . " " . highlightText($row['file_name'], $search);
?>
</td>

<td>
<?php echo highlightText($row['uploader_name'], $search); ?>
</td>

<td><span class="status status-approved">Approved</span></td>

<td>
<div class="action-buttons">
    
    <button class="fav-btn" onclick="togglePin(<?php echo $row['id']; ?>); return false;">
        <?php echo ($row['is_pinned']) ? '📌' : '📍'; ?>
    </button>

  
    <form method="POST" action="toggle_favorite.php" style="display:inline;">
        <input type="hidden" name="note_id" value="<?php echo $row['id']; ?>">
        <button type="submit" class="fav-btn">
            <?php echo ($row['is_favorite']) ? '★' : '☆'; ?>
        </button>
    </form>

    
    <a href="/CNSP/uploads/<?php echo rawurlencode($row['file_name']); ?>"
       class="view-btn"
       onclick="openPopup(this.href); return false;">Download</a>
</div>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="4" style="text-align:center;">No notes available</td>
</tr>
<?php endif; ?>
</table>

<br>
<a class="back-btn" href="/CNSP/Student_dashboard.php">⬅ Back to Dashboard</a>
</div>

<div id="downloadPopup" class="popup">
  <div class="popup-content">
    <p id="popupMessage">Do you want to download this file?</p>
    <div class="popup-buttons">
      <button class="yes-btn" onclick="confirmDownload()">Yes</button>
      <button class="no-btn" onclick="closePopup()">No</button>
    </div>
  </div>
</div>

<script>
let downloadLink = "";
let fileName = "";

function openPopup(link) {
    downloadLink = link;
    const parts = link.split('/');
    fileName = decodeURIComponent(parts[parts.length - 1]);
    document.getElementById("popupMessage").textContent = `Do you want to download "${fileName}"?`;
    document.getElementById("downloadPopup").style.display = "flex";
}

function closePopup() {
    document.getElementById("downloadPopup").style.display = "none";
    downloadLink = "";
    fileName = "";
}

function confirmDownload() {
    window.open(downloadLink, "_blank");
    closePopup();
}


function togglePin(noteId){
    fetch('backend/toggle_pin_note.php?id=' + noteId)
    .then(response => response.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message);
    });
}
</script>

</body>
</html>