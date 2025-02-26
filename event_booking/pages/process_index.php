<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php';  // Include centralized session management
include 'config.php';

// Function to get user's IP address
function getUserIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

// Function to log errors with timestamp, IP, and user agent
function logError($message) {
    $timestamp = date("Y-m-d H:i:s");
    $ip = getUserIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $logEntry = "[$timestamp] [IP: $ip] [User-Agent: $userAgent] $message" . PHP_EOL;

    error_log($logEntry, 3, "error_log.txt");
}

// Initialize error variable
$error = "";

// Check if account is locked before proceeding
if (isset($_SESSION['lockout_time']) && strtotime($_SESSION['lockout_time']) > time()) {
    $error = "Account is locked. Please try again later.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$error) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Server-side validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($email) > 100 || strlen($password) > 72) {
        $error = "Email or password exceeds the allowed length.";
    } else {
        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT id, name, password, role, failed_attempts, lockout_time FROM users WHERE email = ?");
        if (!$stmt) {
            logError("Database error: " . $conn->error);
            die("Database error. Please try again later.");
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Check if the account is locked
            if ($row['lockout_time'] !== null && strtotime($row['lockout_time']) > time()) {
                $error = "Account is locked. Please try again later.";
                $_SESSION['lockout_time'] = $row['lockout_time'];
                logError("Locked account attempt for email: $email");
            } else {
                // If account is not locked, check password
                if (password_verify($password, $row['password'])) {
                    // Reset failed attempts and lockout time on successful login
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE id = ?");
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();

                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['role'] = $row['role'];
                    logError("Successful login for email: $email");

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

                    if ($failed_attempts >= 5) {
                        $lockout_time = date("Y-m-d H:i:s", time() + 30);
                        $_SESSION['lockout_time'] = $lockout_time;
                        logError("Account locked due to multiple failed attempts for email: $email");
                    }

                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_time = ? WHERE id = ?");
                    $stmt->bind_param("isi", $failed_attempts, $lockout_time, $row['id']);
                    $stmt->execute();

                    $error = "Invalid password.";
                    logError("Failed login attempt (invalid password) for email: $email");
                }
            }
        } else {
            $error = "User not found.";
            logError("Failed login attempt - user not found for email: $email");
        }
    }
}

if ($error) {
    $_SESSION['error'] = $error;
    header("Location: index.php");
    exit();
}
?>
