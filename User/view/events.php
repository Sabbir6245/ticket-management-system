<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

$db = new DatabaseConnection();
$conn = $db->openConnection();


$eventsResult = $conn->query("SELECT * FROM events WHERE status='active' AND available_tickets>0");
$events = [];
if ($eventsResult->num_rows > 0) {
    while ($row = $eventsResult->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Events</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; }
        .container { width: 90%; max-width: 900px; margin: 30px auto; background: #fff; padding: 20px; border-radius:6px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #aaa; padding:8px; text-align:left; }
        th { background:#333; color:#fff; }
        tr:nth-child(even){ background:#f2f2f2; }
        input[type=number] { width:60px; }
        input[type=submit] { padding:5px 10px; cursor:pointer; }
        .success { color: green; margin-bottom:10px; }
        .error { color: red; margin-bottom:10px; }
        a.logout { float:right; text-decoration:none; background:#444; color:#fff; padding:5px 10px; border-radius:4px; }
        a.logout:hover{ background:#000; }
        a.home {float:left;text-decoration:none;background:#3498db;color:#fff; padding:5px 12px; border-radius:4px;} 
        a.home:hover{
         background:#2c80b4;}


    </style>

</head>
<body>

<div class="container">
    <a class="home" href="dashboard.php">Home</a>
    <a class="logout" href="../controller/logout.php">Logout</a>
   
    <h2>Available Events</h2>

    <?php if($_SESSION['bookingSuccess'] ?? false): ?>
        <div class="success"><?php echo $_SESSION['bookingSuccess']; unset($_SESSION['bookingSuccess']); ?></div>
    <?php endif; ?>

    <?php if($_SESSION['bookingError'] ?? false): ?>
        <div class="error"><?php echo $_SESSION['bookingError']; unset($_SESSION['bookingError']); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Time</th>
            <th>Venue</th>
            <th>Price</th>
            <th>Available</th>
            <th>Book</th>
        </tr>
        <?php foreach($events as $event): ?>
        <tr>
            <td><?php echo $event['title']; ?></td>
            <td><?php echo $event['description']; ?></td>
            <td><?php echo $event['event_date']; ?></td>
            <td><?php echo $event['event_time']; ?></td>
            <td><?php echo $event['venue']; ?></td>
            <td><?php echo $event['ticket_price']; ?></td>
            <td><?php echo $event['available_tickets']; ?></td>
            <td>
                <form method="post" action="../controller/UserController.php">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <input type="number" name="tickets" min="1" max="<?php echo $event['available_tickets']; ?>" required>
                    <input type="submit" name="book_event" value="Book">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
