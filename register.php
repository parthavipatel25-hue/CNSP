
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link rel="stylesheet" href="css/style-1.css">

<style>
.error{
    border:2px solid red !important;
}
small{
    color:red;
    display:none;
}
</style>

</head>
<body>

<div class="form-box">
<h2>Register</h2>

<form action="backend/register_process.php" method="POST" onsubmit="return validateForm()" autocomplete="off">

<!-- Fake fields -->
<input type="text" name="fakeuser" style="display:none">
<input type="password" name="fakepass" style="display:none">

<!-- ROLE -->
<select name="role" id="role" required>
    <option value="">Select Role</option>
    <option value="student">Student</option>
    <option value="admin">Admin</option>
</select>

<!-- COMMON -->
<input type="text" name="reg_name" placeholder="Full Name" required>

<input type="password" name="reg_password" placeholder="Password" required>

<!-- STUDENT -->
<div id="studentFields" style="display:none;">

<input type="email" id="studentEmail" name="student_email"
placeholder="StudentId@charusat.edu.in">

<small id="studentError">Enter valid ID like 22abc123@charusat.edu.in</small>

<select name="semester">
<option value="">Select Semester</option>
<option>1</option>
<option>2</option>
<option>3</option>
<option>4</option>
<option>5</option>
<option>6</option>
<option>7</option>
<option>8</option>
</select>

<input type="text" name="university" placeholder="University name">

</div>

<!-- ADMIN -->
<div id="adminFields" style="display:none;">

<input type="email" id="adminEmail" name="admin_email"
placeholder="admin@charusat.ac.in">

<small id="adminError">Email must be @charusat.ac.in</small>

</div>

<button type="submit">Register</button>

</form>

<p>Already registered? <a href="login.php">Login</a></p>

</div>

<script>

const role = document.getElementById("role");
const studentFields = document.getElementById("studentFields");
const adminFields = document.getElementById("adminFields");

const studentEmail = document.getElementById("studentEmail");
const adminEmail = document.getElementById("adminEmail");

const studentError = document.getElementById("studentError");
const adminError = document.getElementById("adminError");

/* SHOW/HIDE + REQUIRED */
role.addEventListener("change", function(){

    if(role.value === "student"){
        studentFields.style.display = "block";
        adminFields.style.display = "none";

        studentEmail.required = true;
        adminEmail.required = false;
    }
    else if(role.value === "admin"){
        studentFields.style.display = "none";
        adminFields.style.display = "block";

        studentEmail.required = false;
        adminEmail.required = true;
    }
    else{
        studentFields.style.display = "none";
        adminFields.style.display = "none";

        studentEmail.required = false;
        adminEmail.required = false;
    }

});

/* VALIDATION */
function validateForm(){

    let selectedRole = role.value;

    if(selectedRole === "student"){
        let email = studentEmail.value;
        let pattern = /^[0-9]{2}[a-zA-Z]+[0-9]+@charusat\.edu\.in$/;

        if(!pattern.test(email)){
            studentEmail.classList.add("error");
            studentError.style.display = "block";
            return false;
        } else {
            studentEmail.classList.remove("error");
            studentError.style.display = "none";
        }
    }

    if(selectedRole === "admin"){
        let email = adminEmail.value;

        if(!email.endsWith("@charusat.ac.in")){
            adminEmail.classList.add("error");
            adminError.style.display = "block";
            return false;
        } else {
            adminEmail.classList.remove("error");
            adminError.style.display = "none";
        }
    }

    return true;
}

/* RESET FORM */
window.onload = function(){
    document.querySelector("form").reset();
};

</script>

</body>
</html>