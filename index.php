<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Notes Sharing Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
session_start();

/* Logout logic */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>


<!-- Navbar -->
<nav class="navbar">
    <div class="logo">CNSP</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="#how">How It Works</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="btn-nav">Register</a></li>
    </ul>
</nav>

<!-- Hero -->
<section class="hero">
    <h1>Secure College Notes Sharing Platform</h1>
    <p>Organize • Verify • Access academic notes with confidence</p>
    <a href="register.php" class="btn-primary">Get Started</a>
</section>

<!-- Stats -->
<section class="stats">
    <div>
        <h2>500+</h2>
        <p>Notes Uploaded</p>
    </div>
    <div>
        <h2>200+</h2>
        <p>Active Students</p>
    </div>
    <div>
        <h2>100%</h2>
        <p>Verified Content</p>
    </div>
</section>

<!-- How it works -->
<section id="how" class="how">
    <h2>How It Works</h2>
    <div class="steps">
        <div class="step">1️⃣ Student Uploads Notes</div>
        <div class="step">2️⃣ Admin Reviews Content</div>
        <div class="step">3️⃣ Students Download Securely</div>
    </div>
</section>

<!-- Features -->
<section id="features" class="features">
    <h2>Key Features</h2>
    <div class="feature-box">
        <div class="card">
            <h3>Role-Based Access</h3>
            <p>Separate dashboards for students and administrators.</p>
        </div>
        <div class="card">
            <h3>Approval System</h3>
            <p>Admin ensures only quality notes are published.</p>
        </div>
        <div class="card">
            <h3>Analytics</h3>
            <p>Track downloads and popular study material.</p>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta">
    <h2>Start Sharing Knowledge Today</h2>
    <p>Join your classmates on a secure academic platform.</p>
    <a href="register.php" class="btn-primary">Create Account</a>
</section>

<!-- Footer -->
<footer class="footer">
    <p>© 2025 Secure College Notes Sharing Platform</p>
</footer>

</body>
</html>
