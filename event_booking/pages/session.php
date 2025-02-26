<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

// Start the session only if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0); // Secure cookies only on HTTPS
    ini_set('session.use_strict_mode', 1);
    
    session_start();
}

// Prevent session fixation by regenerating the session ID once per session
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// List of **public pages** where session expiration should NOT happen
$public_pages = array('login.php', 'signup.php', 'index.php');

// Define session timeout duration (30 seconds for testing)
$timeout_duration = 30;

// **Only enforce session expiration if the user is on a protected page (not in public pages)**
if (!in_array($current_page, $public_pages) && isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: index.php?session=expired");
        exit();
    }
}

// **Update last activity time only for logged-in users**
if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Functions for enforcing login (admin or user)
function require_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }
}

function require_user_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
}

// Error handling
if (isset($_SESSION['error'])) {
    if (!isset($_SESSION['error_time'])) {
        $_SESSION['error_time'] = time();
    }
    if (time() - $_SESSION['error_time'] >= 10) {
        unset($_SESSION['error']);
        unset($_SESSION['error_time']);
    }
}
?>
