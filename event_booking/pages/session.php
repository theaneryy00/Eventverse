<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

// Define log file
define("ERROR_LOG_FILE", "error_log.txt");

// Function to log errors
function log_error($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, ERROR_LOG_FILE);
}

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0); // Secure cookies only on HTTPS
    ini_set('session.use_strict_mode', 1);

    if (!session_start()) {
        log_error("Failed to start session.");
    }
}

// Prevent session fixation
if (!isset($_SESSION['regenerated'])) {
    if (!session_regenerate_id(true)) {
        log_error("Failed to regenerate session ID.");
    }
    $_SESSION['regenerated'] = true;
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// List of **public pages** where session expiration should NOT happen
$public_pages = array('login.php', 'signup.php', 'index.php');

// Define session timeout duration (30 seconds for testing)
$timeout_duration = 30;

// **Session expiration check for protected pages**
if (!in_array($current_page, $public_pages) && isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        log_error("Session expired for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
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
        log_error("Unauthorized access attempt to admin page.");
        header("Location: index.php");
        exit();
    }
}

function require_user_login() {
    if (!isset($_SESSION['user_id'])) {
        log_error("Unauthorized access attempt to user page.");
        header("Location: index.php");
        exit();
    }
}

// Error handling with logging
if (isset($_SESSION['error'])) {
    if (!isset($_SESSION['error_time'])) {
        $_SESSION['error_time'] = time();
    }
    if (time() - $_SESSION['error_time'] >= 10) {
        log_error("Clearing session error: " . $_SESSION['error']);
        unset($_SESSION['error']);
        unset($_SESSION['error_time']);
    }
}
?>
