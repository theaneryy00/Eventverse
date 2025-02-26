<?php
// Security headers to prevent clickjacking
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");
require_once 'session.php'; // Centralized session management file
require_admin_login();      // Enforce admin login

include 'config.php';



// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

function validate_event_name($name) {
    return preg_match("/^[a-zA-Z0-9\s\-'.]{1,100}$/", $name); // Restricts length & special characters
}

function validate_event_date($date) {
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date) && strtotime($date) >= strtotime(date("Y-m-d"));
}

// Handle event creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $name = trim($_POST['name']);
    $date = trim($_POST['date']);

    if (empty($name) || empty($date)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "error";
    } elseif (!validate_event_name($name)) {
        $_SESSION['message'] = "Event name must be 1-100 characters and contain only letters, numbers, spaces, hyphens, apostrophes, and periods.";
        $_SESSION['message_type'] = "error";
    } elseif (!validate_event_date($date)) {
        $_SESSION['message'] = "Invalid event date. You cannot select a past date.";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO events (name, date) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $date);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Event added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding event.";
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    header("Location: manage_events.php");
    exit();
}

// Handle event update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    $id = $_POST['event_id'];
    $name = trim($_POST['name']);
    $date = trim($_POST['date']);

    if (empty($name) || empty($date)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "error";
    } elseif (!validate_event_name($name)) {
        $_SESSION['message'] = "Event name must be 1-100 characters and contain only letters, numbers, spaces, hyphens, apostrophes, and periods.";
        $_SESSION['message_type'] = "error";
    } elseif (!validate_event_date($date)) {
        $_SESSION['message'] = "Invalid event date. You cannot select a past date.";
        $_SESSION['message_type'] = "error";
    } else {
        $stmt = $conn->prepare("UPDATE events SET name=?, date=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $date, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Event updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating event.";
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    header("Location: manage_events.php");
    exit();
}

// Fetch all events
$events = $conn->query("SELECT id, name, date FROM events");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js"></script> <!-- External JS file -->
</head>
<body class="admin-layout">

<div class="sidebar">
    <img src="/event_booking/images/logo.png" alt="Event Booking Logo" class="logo">
    <h2 class="admin-title">Admin Dashboard</h2><hr>
    <ul>
        <li><a href="admin_dashboard.php">Overview</a></li>
        <li><a href="manage_events.php">Manage Events</a></li>
        <li><a href="sales_report.php">Sales Report</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Manage Events</h1>

    <!-- Success/Error Message Container -->
    <div id="notification" 
         data-message="<?php echo $_SESSION['message'] ?? ''; ?>" 
         data-type="<?php echo $_SESSION['message_type'] ?? ''; ?>">
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>

    <!-- Add Event Form -->
    <div class="form-container">
        <h3>Add Event</h3>
        <form method="post">
            <table>
                <tr>
                    <td><label>Event Name:</label></td>
                    <td><input type="text" name="name" placeholder="Event Name" required></td>
                </tr>
                <tr>
                    <td><label>Event Date:</label></td>
                    <td><input type="date" name="date" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="add_event">Add Event</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    
    <br>
    <h3>Existing Events</h3><br>
    <table class="event-table">
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $events->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td>
                    <button class="edit-btn" data-id="<?php echo $row['id']; ?>" 
                            data-name="<?php echo htmlspecialchars(addslashes($row['name'])); ?>" 
                            data-date="<?php echo htmlspecialchars($row['date']); ?>">
                        Edit
                    </button>
                    <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="delete-btn">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Edit Event Modal (Hidden by Default) -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Event</h3>
        <form method="post">
            <table>
                <tr>
                    <td><label>Event Name:</label></td>
                    <td><input type="text" name="name" id="edit_name" required></td>
                </tr>
                <tr>
                    <td><label>Event Date:</label></td>
                    <td><input type="date" name="date" id="edit_date" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="event_id" id="edit_event_id">
                        <button type="submit" name="update_event">Update Event</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


</body>
</html>
