<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>

    <!-- Reuse profile styling -->
    <link rel="stylesheet" href="/CNSP/css/profile.css">

    <!-- Small extra tweaks only -->
    <style>
        form {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 500px;
            margin-top: 20px;
        }

        label {
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-primary);
        }

        input[type="text"],
        input[type="email"] {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 1rem;
        }

        button {
            padding: 12px;
            background-color: var(--success);
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,211,153,0.3);
        }

        .back-btn {
            margin-top: 15px;
            display: inline-block;
            padding: 12px 24px;
            background: rgba(99,102,241,0.05);
            color: var(--primary-light);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(99,102,241,0.15);
            color: var(--primary);
            transform: translateX(-4px);
        }
    </style>
</head>

<body>

<div class="profile-container">
    <h2>Edit Student</h2>

    <form method="post" action="backend/update_student.php">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>Name</label>
        <input type="text" name="name"
               value="<?= htmlspecialchars($student['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($student['email']) ?>" required>

        <button type="submit">Update Student</button>
    </form>

    <a class="back-btn" href="/CNSP/admin_users.php"> Back</a>
</div>

</body>
</html>