<?php
// Security headers to prevent clickjacking
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php'; // Centralized session handling
require_admin_login(); // Ensure only admins can access
require_once 'config.php'; // Database connection

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Check if event ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Event deleted successfully!";
        $_SESSION['message_type'] = "success"; // Store success message
    } else {
        $_SESSION['message'] = "Error deleting event.";
        $_SESSION['message_type'] = "error";
        error_log("[ERROR] Failed to delete event ID $id: " . $conn->error, 3, "error_log.txt");
    }
}
header("Location: manage_events.php"); // Redirect after deletion
exit();
?>
