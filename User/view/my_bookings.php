<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'] ?? 0;


$db = new DatabaseConnection();
$conn = $db->openConnection();


$stmt = $conn->prepare("
    SELECT b.id AS booking_id, b.tickets_booked, b.booked_at, 
           e.title, e.event_date, e.venue, e.ticket_price
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE b.user_id = ?
    ORDER BY b.booked_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);


$bookingMessage = $_SESSION['bookingMessage'] ?? '';
unset($_SESSION['bookingMessage']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .container { width: 90%; max-width: 900px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        .cancel-btn { background: #c0392b; color: #fff; padding: 5px 10px; border: none; cursor: pointer; border-radius: 4px; }
        .cancel-btn:hover { background: #e74c3c; }
        .message { background: #2ecc71; color: #fff; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        a.home {float:left;text-decoration:none;background:#3498db;color:#fff; padding:5px 12px; border-radius:4px;} 
        a.home:hover{
         background:#2c80b4;}
   </style>
</head>
<body>

<div class="container">
    <a class="home" href="dashboard.php">Home</a>


    <h2>My Bookings</h2>

    <?php if($bookingMessage): ?>
        <div class="message"><?php echo $bookingMessage; ?></div>
    <?php endif; ?>

    <?php if(count($bookings) > 0): ?>
        <table>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Tickets</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach($bookings as $b): ?>
            <tr>
                <td><?php echo $b['title']; ?></td>
                <td><?php echo $b['event_date']; ?></td>
                <td><?php echo $b['venue']; ?></td>
                <td><?php echo $b['tickets_booked']; ?></td>
                <td><?php echo $b['ticket_price']; ?></td>
                <td><?php echo $b['tickets_booked'] * $b['ticket_price']; ?></td>
                <td>
                    <form method="post" action="../controller/cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                        <input type="submit" name="cancel" class="cancel-btn" value="Cancel">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
