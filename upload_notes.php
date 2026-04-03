
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/backend/db.php';

/* Restrict page to students */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Access denied");
}

$message = "";
$messageType = "";

$user_id = $_SESSION['user_id'];

/* 📅 Get today's uploads count */
$today = date('Y-m-d');

$countQuery = mysqli_query($conn,
"SELECT COUNT(*) AS total 
 FROM notes 
 WHERE uploaded_by = '$user_id' 
 AND DATE(uploaded_at) = '$today'");

$count = mysqli_fetch_assoc($countQuery)['total'];
$remaining = 5 - $count;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['notes_file'])) {

    /* 🚫 CHECK DAILY LIMIT */
    if ($count >= 5) {
        $message = "Upload limit reached! Only 5 notes per day allowed.";
        $messageType = "error";
    } else {

        $file = $_FILES['notes_file'];

        /* 📏 FILE SIZE LIMIT (1GB) */
        $maxSize = 1024 * 1024 * 1024;

        if ($file['size'] > $maxSize) {

            $message = "File too large! Maximum allowed size is 1GB.";
            $messageType = "error";

        } else {

            $originalName = basename($file['name']);
            $noteType = $_POST['note_type'] ?? "";

            $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {

                $message = "Invalid file type";
                $messageType = "error";

            } elseif (empty($noteType)) {

                $message = "Please select file type";
                $messageType = "error";

            } else {

                $validType = false;

                if ($noteType === 'PDF' && $ext === 'pdf') $validType = true;
                if ($noteType === 'DOC' && in_array($ext, ['doc','docx'])) $validType = true;
                if ($noteType === 'PPT' && in_array($ext, ['ppt','pptx'])) $validType = true;

                if (!$validType) {

                    $message = "File extension does not match selected type";
                    $messageType = "error";

                } else {

                    $uploadDir = __DIR__ . '/uploads/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    /* =========================
                       🔥 VERSION LOGIC
                    ========================== */
                    $stmt = $conn->prepare(
                        "SELECT MAX(version) as max_version 
                         FROM notes 
                         WHERE uploaded_by = ? AND original_name = ?"
                    );

                    if ($stmt) {
                        $stmt->bind_param("is", $user_id, $originalName);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $row = $res->fetch_assoc();
                        $version = ($row && $row['max_version']) ? $row['max_version'] + 1 : 1;
                    } else {
                        $version = 1; // fallback
                    }

                    /* =========================
                       🔥 FILE RENAME LOGIC
                    ========================== */
                    $fileName = $originalName;
                    $counter = 1;

                    while(file_exists($uploadDir . $fileName)){
                        $fileName = pathinfo($originalName, PATHINFO_FILENAME)
                                  . "($counter)."
                                  . pathinfo($originalName, PATHINFO_EXTENSION);
                        $counter++;
                    }

                    /* =========================
                       UPLOAD FILE
                    ========================== */
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {

                        $stmt = $conn->prepare(
                        "INSERT INTO notes (file_name, original_name, note_type, uploaded_by, version, status)
                         VALUES (?, ?, ?, ?, ?, 'pending')"
                        );

                        if ($stmt) {

                            $stmt->bind_param(
                                "sssii",
                                $fileName,
                                $originalName,
                                $noteType,
                                $user_id,
                                $version
                            );

                            if (!$stmt->execute()) {
                                die("Insert Error: " . $stmt->error);
                            }

                        } else {
                            die("Prepare Error: " . $conn->error);
                        }

                        /* activity log */
                        mysqli_query($conn,
                        "INSERT INTO activity_logs (user_id, action)
                         VALUES ($user_id, 'Uploaded a note')");

                        $message = "Notes uploaded successfully (v$version)";
                        $messageType = "success";

                    } else {

                        $message = "Upload failed";
                        $messageType = "error";

                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Notes</title>
<link rel="stylesheet" href="css/upload_notes.css">
</head>

<body>

<div class="container">

<h2>Upload Notes</h2>

<p style="text-align:center; font-weight:bold;">
You can upload <span style="color:green;"><?php echo max(0, $remaining); ?></span> more notes today
</p>

<?php if (!empty($message)) : ?>
<div class="message <?= $messageType ?>">
<?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="upload-box">

<div class="file-row">
<label class="custom-file-btn">
CHOOSE FILE
<input type="file" name="notes_file" required hidden>
</label>

<span class="file-name">No file chosen</span>
</div>

<select name="note_type" class="note-type-select" required>
<option value="">-- Select File Type --</option>
<option value="PDF">PDF</option>
<option value="DOC">DOC</option>
<option value="PPT">PPT</option>
</select>

</div>

<button type="submit" class="upload-btn">UPLOAD</button>

</form>

<br>

<a class="back-btn" href="/CNSP/Student_dashboard.php">
⬅ Back to Dashboard
</a>

</div>

<script>
document.querySelector('input[type="file"]').addEventListener('change', function () {
document.querySelector('.file-name').textContent = this.files[0].name;
});
</script>

</body>
</html>