<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style-1.css">
</head>
<body>

<div class="form-box">
    <h2>Login</h2>

    <form action="backend/login_process.php" method="POST" autocomplete="off">

        <input type="text" name="fakeuser" style="display:none">
        <input type="password" name="fakepass" style="display:none">

        <input type="email" name="login_email" placeholder="Email" required autocomplete="off">

        <input type="password" name="login_password" placeholder="Password" required autocomplete="new-password">

        <button type="submit">Login</button>
    </form>

    <p>New user? <a href="register.php">Register</a></p>

</div>

<script>
window.onload = function(){
    document.querySelector("form").reset();
};
</script>


</body>
</html>