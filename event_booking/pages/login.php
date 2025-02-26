<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

session_start();  // Include centralized session management
include 'config.php';


// Redirect logged-in users
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js" defer></script> 
    
</head>
<body>
    <div class="container">
        <img src="/event_booking/images/logo.png" alt="Event Booking Logo" class="logo">
        <h2>Login</h2>

        <!-- Display errors -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" id="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form id="login-form" action="process_login.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Email" maxlength="100" required><br>
            <input type="password" id="password" name="password" placeholder="Password" minlength="8" maxlength="72" required><br>
            <button type="submit">Login</button>
        </form>

        

        <p>Don't have an account? <a href="signup.php">Sign up instead</a></p>
    </div>
</body>
</html>
