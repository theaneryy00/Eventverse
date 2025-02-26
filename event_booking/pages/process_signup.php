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
    header("Location: signup.php");
    exit();
}

// Set custom error handler
set_error_handler("customError");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Strict Input Validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: signup.php");
        exit();
    }

    if (strlen($name) > 100) {
        $_SESSION['error'] = "Full Name must not exceed 100 characters.";
        header("Location: signup.php");
        exit();
    }

    // Validates the name
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) { // Allowing hyphens & apostrophes
        $_SESSION['error'] = "Full Name must not contain special characters.";
        header("Location: signup.php");
        exit();
    }

    // Validates the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: signup.php");
        exit();
    }

    if (strlen($email) > 100 || strlen($password) > 72) {
        $_SESSION['error'] = "Email or password exceeds the allowed length.";
        header("Location: signup.php");
        exit();
    }

    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and include one letter, one number, and one special character.";
        header("Location: signup.php");
        exit();
    }

    // ✅ Check if email already exists
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Email is already registered. Try logging in.";
            $stmt->close();
            header("Location: signup.php");
            exit();
        }
    } catch (Exception $e) {
        // Catch any database-related errors and log them
        error_log("Database error: " . $e->getMessage(), 3, "error_log.txt");
        $_SESSION['error'] = "An error occurred. Please try again later.";
        header("Location: signup.php");
        exit();
    }

    // ✅ Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert new user
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id; // Auto-login after signup
            header("Location: dashboard.php");
            exit();
        } else {
            throw new Exception("Error executing query.");
        }
    } catch (Exception $e) {
        // Log the failure and show user-friendly message
        error_log("Database error during signup: " . $e->getMessage(), 3, "error_log.txt");
        $_SESSION['error'] = "Error in registration. Please try again.";
        header("Location: signup.php");
        exit();
    }

    $stmt->close();
}
?>
