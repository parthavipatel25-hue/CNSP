<?php
session_start();
require __DIR__ . '/backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = "";

/* Fetch user data */
$stmt = $conn->prepare("SELECT name,email,password FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Update profile */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = $_POST['name'];
    $email = $_POST['email'];

    $current_password = $_POST['current_password'] ?? "";
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";

    /* If user wants to change password */
    if(!empty($current_password) || !empty($new_password) || !empty($confirm_password)){

        if(!password_verify($current_password,$user['password'])){
            $error = "Current password is incorrect.";
        }
        elseif($new_password != $confirm_password){
            $error = "New passwords do not match.";
        }
        elseif(strlen($new_password) < 6){
            $error = "Password must be at least 6 characters.";
        }
        else{

            $new_hash = password_hash($new_password,PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET name=?,email=?,password=? WHERE id=?");
            $update->bind_param("sssi",$name,$email,$new_hash,$user_id);
            $update->execute();

            header("Location: profile.php");
            exit;
        }

    }else{

        /* Only update name and email */
        $update = $conn->prepare("UPDATE users SET name=?,email=? WHERE id=?");
        $update->bind_param("ssi",$name,$email,$user_id);
        $update->execute();

        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Profile</title>
<link rel="stylesheet" href="css/edit_profile.css">

</head>

<body>

<div class="profile-container">

<h2>Edit Profile</h2>

<?php if($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="post">

<label>Name:</label>
<input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

<label>Email:</label>
<input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

<!-- Update Password Button -->
<button type="button" class="edit-btn" onclick="togglePassword()">Update Password</button>

<!-- Hidden Password Fields -->
<div id="password-section" style="display:none;">

<label>Current Password:</label>
<input type="password" name="current_password" placeholder="Enter current password">

<label>New Password:</label>
<input type="password" name="new_password" placeholder="Enter new password">

<label>Confirm New Password:</label>
<input type="password" name="confirm_password" placeholder="Confirm new password">

</div>

<input type="submit" value="Update Profile">

<a href="profile.php" class="cancel-btn">Cancel</a>

</form>

</div>

<script>

function togglePassword(){

    var section = document.getElementById("password-section");

    if(section.style.display === "none"){
        section.style.display = "block";
    }
    else{
        section.style.display = "none";
    }

}

</script>


</body>
</html>