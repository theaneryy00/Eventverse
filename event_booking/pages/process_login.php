<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php';  // Include centralized session management
include 'config.php';

// Set up custom error handler for better error management
function customError($errno, $errstr, $errfile, $errline) {
    // Log the error in a file
    error_log("Error [$errno]: $errstr in $errfile on line $errline", 3, "error_log.txt");

    // Display a user-friendly message
    $_SESSION['error'] = "Something went wrong. Please try again later.";
    header("Location: login.php");
    exit();
}

// Set custom error handler
set_error_handler("customError");

// Initialize error variable
$error = "";

// Check if account is locked before proceeding
if (isset($_SESSION['lockout_time']) && strtotime($_SESSION['lockout_time']) > time()) {
    $error = "Account is locked. Please try again later.";
}

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$error) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Server-side validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($email) > 100 || strlen($password) > 72) {
        $error = "Email or password exceeds the allowed length.";
    } else {
        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT id, name, password, role, failed_attempts, lockout_time FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Check if the account is locked
            if ($row['lockout_time'] !== null && strtotime($row['lockout_time']) > time()) {
                $error = "Account is locked. Please try again later.";
                // Store lockout time in session
                $_SESSION['lockout_time'] = $row['lockout_time'];
            } else {
                // If account is not locked, check password
                if (password_verify($password, $row['password'])) {
                    // Reset failed attempts and lockout time on successful login
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE id = ?");
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();

                    // Set session variables
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['role'] = $row['role'];

                    // Redirect based on role
                    if ($row['role'] === 'admin') {
                        $_SESSION['admin_id'] = $row['id'];
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: dashboard.php");
                    }
                    exit();
                } else {
                    // Increment failed attempts
                    $failed_attempts = $row['failed_attempts'] + 1;
                    $lockout_time = null;

                    // If failed attempts exceed the limit, set lockout time (e.g., 30 seconds lockout)
                    if ($failed_attempts >= 5) {
                        $lockout_time = date("Y-m-d H:i:s", time() + 30); // 30 seconds lockout

                        // Store lockout time in session
                        $_SESSION['lockout_time'] = $lockout_time;
                    }

                    // Update failed attempts and lockout time in the database
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_time = ? WHERE id = ?");
                    $stmt->bind_param("isi", $failed_attempts, $lockout_time, $row['id']);
                    $stmt->execute();

                    $error = "Invalid password.";
                    // Log the failed attempt
                    error_log("Failed login attempt for email: $email", 3, "error_log.txt");
                }
            }
        } else {
            $error = "User  not found.";
            // Log the failed attempt
            error_log("Failed login attempt - user not found for email: $email", 3, "error_log.txt");
        }
    }
}

// Store error in session to display in login page
if ($error) {
    $_SESSION['error'] = $error;
    header("Location: login.php");
    exit();
}
?>