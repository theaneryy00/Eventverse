<?php
// Security headers
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php'; // Centralized session handling
require_user_login(); // Enforce user login

require_once 'config.php'; // Database connection


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Event Booking</title>
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js" defer></script>

</head>
<body>

    

    <div class="sidebar">
    <img src="/event_booking/images/logo.png" alt="Event Booking Logo" class="logo">
    <h2 class="admin-title">Dashboard</h2><hr>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="view_events.php">View Events</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        
    <img src="/event_booking/images/event.gif" alt="Event Planet" class="event-logo">

        <h1>Welcome to EventVerse!</h1>
        <p>Explore upcoming events and book your tickets now.</p>
        
        <?php if (isset($_SESSION['success'])): ?>

            <div class="success-message">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <a href="view_events.php" class="btn">View Events</a>

        
    </div>

    

</body>
</html>
