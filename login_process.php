<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid Request");
}

// GET DATA
$email = $_POST['login_email'];
$password = $_POST['login_password'];

if (empty($email) || empty($password)) {
    die("All fields are required.");
}

// OPTIONAL: EMAIL FORMAT CHECK
if (
    !preg_match("/^[0-9]{2}[a-zA-Z]+[0-9]+@charusat\.edu\.in$/", $email) &&
    !str_ends_with($email, "@charusat.ac.in")
) {
    die("Invalid email format");
}

// SECURE QUERY
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("User not found");
}

$user = $result->fetch_assoc();

// PASSWORD CHECK
if (!password_verify($password, $user['password'])) {
    die("Wrong password");
}

// SESSION
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

// ACTIVITY LOG
$logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
$action = "Logged in";
$logStmt->bind_param("is", $user['id'], $action);
$logStmt->execute();

// REDIRECT BASED ON ROLE (AUTO)
if ($user['role'] === 'admin') {
    header("Location: ../admin_dashboard.php");
} else {
    header("Location: ../student_dashboard.php");
}

exit();
?>