<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php';  // Include centralized session management
include 'config.php';

// Redirect logged-in users to dashboard
if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js" defer></script>
    <title>Index</title>
</head>
<body>
<div class="container">
    <!-- Login Section -->
    <div class="box">
        <img src="/event_booking/images/logo.png" alt="Event Booking Logo">
        <h2>Login</h2>

        <!-- Display errors -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form id="login-form" action="process_index.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Email" maxlength="100" required><br>
            <input type="password" id="password" name="password" placeholder="Password" minlength="8" maxlength="72" required><br>
            <button type="submit">Login</button>
        </form>
    </div>

    <!-- Sign-up Section -->
    <div class="box">
        <h2>New Here?</h2>
        <p>Create an account to book events easily!</p>
        <button onclick="window.location.href='signup.php'">Sign Up</button>
    </div>
</div>
</body>
</html>
