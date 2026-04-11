<?php
$host = "localhost";   
$user = "root";
$pass = "";
$db   = "college_notes";
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("DB CONNECTION FAILED: " . mysqli_connect_error());
}
?>