
<?php
session_start();
require __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid Request");
}

/* GET DATA */
$name = $_POST['reg_name'] ?? '';
$password_raw = $_POST['reg_password'] ?? '';
$role = $_POST['role'] ?? '';

/* GET EMAIL BASED ON ROLE */
if ($role === "student") {
    $email = $_POST['student_email'] ?? '';
} elseif ($role === "admin") {
    $email = $_POST['admin_email'] ?? '';
} else {
    $email = '';
}

/* VALIDATION */
if (empty($name) || empty($email) || empty($password_raw) || empty($role)) {
    die("All fields are required.");
}

/* EMAIL VALIDATION */
if ($role === "student") {
    if (!preg_match("/^[0-9]{2}[a-zA-Z]+[0-9]+@charusat\.edu\.in$/", $email)) {
        die("Invalid student email format.");
    }
}

if ($role === "admin") {
    if (!str_ends_with($email, "@charusat.ac.in")) {
        die("Admin email must be @charusat.ac.in");
    }
}

/* HASH PASSWORD */
$password = password_hash($password_raw, PASSWORD_DEFAULT);

/* CHECK DUPLICATE */
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("This email is already registered.");
}

/* INSERT USER */
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {

    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['role'] = $role;

    if ($role === "admin") {
        header("Location: ../admin_dashboard.php");
    } else {
        header("Location: ../student_dashboard.php");
    }
    exit();

} else {
    die("Error: " . $stmt->error);
}
?>