<?php
// Security headers
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-cdn.com; style-src 'self'; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; form-action 'self'; connect-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests");
include 'session.php'; // ✅ Centralized session management
require_user_login(); // ✅ Ensure user is logged in

include 'config.php';



// Validate event ID
$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    $_SESSION['error'] = "An error occurred. Please try again.";
    header("Location: events.php");
    exit();
}

// Fetch event details
$stmt = $conn->prepare("SELECT name FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->bind_result($event_name);
$stmt->fetch();
$stmt->close();

// Fetch booked seats for this event
$booked_seats = [];
$result = $conn->query("SELECT seat_number FROM tickets WHERE event_id = $event_id");
while ($row = $result->fetch_assoc()) {
    $booked_seats[] = $row['seat_number'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticket_category = $_POST['category'] ?? null;
    $seat_number = $_POST['seat_number'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$ticket_category || !$seat_number) {
        $_SESSION['error'] = "Please complete all required fields.";
        header("Location: book_ticket.php?event_id=$event_id");
        exit();
    }

    // Check if seat is already booked
    if (in_array($seat_number, $booked_seats)) {
        $_SESSION['error'] = "Selected seat is unavailable. Please choose another.";
        header("Location: book_ticket.php?event_id=$event_id");
        exit();
    }

    // Set price based on category
    $ticket_price = ($ticket_category == 'VIP') ? 500.00 : 250.00;

    // Insert ticket into database
    $stmt = $conn->prepare("INSERT INTO tickets (event_id, user_id, category, seat_number, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $event_id, $user_id, $ticket_category, $seat_number, $ticket_price);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Your ticket has been booked successfully for '$event_name'.";
        header("Location: book_ticket.php?event_id=$event_id"); // Stay on page for JS to handle redirection
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket</title>
    <link rel="stylesheet" href="/event_booking/css/style.css">
    <script src="/event_booking/js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <h2>Book Ticket for <?php echo htmlspecialchars($event_name); ?></h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Category:</label>
            <select name="category" id="ticket_category" required>
                <option value="VIP">VIP - ₱500.00</option>
                <option value="Regular">Regular - ₱250.00</option>
            </select><br>

            <label>Seat Number:</label>
            <select name="seat_number" required>
                <?php for ($i = 1; $i <= 50; $i++): ?>
                    <?php if (!in_array($i, $booked_seats)): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
            </select><br>

            <label>Price:</label>
            <span id="ticket_price">₱500.00</span><br> <!-- Default price is VIP -->

            <button type="submit">Book Now</button>
        </form>

        
    </div>
</body>
</html>
