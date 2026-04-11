<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get user details */
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Only count notes if user is a student */
$total_notes = 0;

if ($user['role'] === 'student') {

    $count_stmt = $conn->prepare("SELECT COUNT(*) AS total_notes FROM notes WHERE uploaded_by = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $note_data = $count_result->fetch_assoc();
    $total_notes = $note_data['total_notes'];

}

/* Dashboard redirect */
$dashboard = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'student_dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link rel="stylesheet" href="css/profile.css">
</head>

<body>

<div class="profile-container">

<h2>My Profile</h2>

<table class="profile-table">

<tr>
<th>Field</th>
<th>Details</th>
</tr>

<tr>
<td>Name:</td>
<td><?= htmlspecialchars($user['name']) ?></td>
</tr>

<tr>
<td>Email:</td>
<td><?= htmlspecialchars($user['email']) ?></td>
</tr>

<tr>
<td>Role:</td>
<td><?= htmlspecialchars($user['role']) ?></td>
</tr>

<?php if ($user['role'] === 'student'): ?>

<tr>
<td>Total Notes Uploaded:</td>
<td><?= $total_notes ?></td>
</tr>

<?php endif; ?>

</table>

<a href="edit_profile.php" class="edit-btn">Edit Profile</a>

<a href="<?= $dashboard ?>" class="back-btn">Back to Dashboard</a>

</div>

</body>
</html>