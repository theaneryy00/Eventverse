<?php
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");

require_once 'session.php'; // Centralized session management file
require_admin_login();      // Enforce admin login

include 'config.php';



if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$sales = $conn->query("
    SELECT events.name, COUNT(tickets.id) as total_sold, SUM(tickets.price) as total_revenue
    FROM tickets 
    JOIN events ON tickets.event_id = events.id 
    GROUP BY events.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js" defer></script>
   
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
        <div class="sales-container">
            <h1>Sales Report</h1>
            <table class="sales-table">
                <tr>
                    <th>Event</th>
                    <th>Tickets Sold</th>
                    <th>Total Revenue</th>
                </tr>
                <?php while ($row = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['total_sold']; ?></td>
                        <td>₱<?php echo number_format($row['total_revenue'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

</body>
</html>
