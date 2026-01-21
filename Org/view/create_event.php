<?php
session_start();
include "../model/DatabaseConnection.php";

// Check login
if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

$organiser_id = $_SESSION['user_id']; 
$db = new DatabaseConnection();
$conn = $db->openConnection();


$stmt = $conn->prepare("SELECT * FROM events WHERE organiser_id=? ORDER BY event_date DESC");
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);


$success = $_SESSION['eventSuccess'] ?? '';
$error   = $_SESSION['eventError'] ?? '';
unset($_SESSION['eventSuccess'], $_SESSION['eventError']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Management</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 6px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        input[type="text"], input[type="date"], input[type="time"], input[type="number"], textarea { width: 100%; padding: 5px; box-sizing: border-box; }
        input[type="submit"], .delete-btn { padding: 5px 10px; cursor: pointer; }
        .delete-btn { background: red; color: #fff; border: none; border-radius: 3px; text-decoration: none; display: inline-block; }
        .success { color: green; text-align: center; margin-bottom: 10px; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
        .form-container { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Event Management</h2>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>

   
    <div class="form-container">
        <h3>Create New Event</h3>
        <form method="post" action="../controller/EventController.php">
            <input type="text" name="title" placeholder="Event Title" required><br><br>
            <textarea name="description" placeholder="Event Description" required></textarea><br><br>
            <input type="date" name="event_date" required>
            <input type="time" name="event_time" required><br><br>
            <input type="text" name="venue" placeholder="Venue" required><br><br>
            <input type="number" name="total_tickets" placeholder="Total Tickets" required>
            <input type="number" name="ticket_price" placeholder="Ticket Price" required><br><br>
            <input type="submit" name="create" value="Create Event">
        </form>
    </div>

    
    <h3>My Events</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Time</th>
            <th>Venue</th>
            <th>Total Tickets</th>
            <th>Available Tickets</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php foreach($events as $event): ?>
        <tr>
            <form method="post" action="../controller/EventController.php">
                <td><?= $event['id'] ?></td>
                <td><input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>"></td>
                <td><input type="text" name="description" value="<?= htmlspecialchars($event['description']) ?>"></td>
                <td><input type="date" name="event_date" value="<?= $event['event_date'] ?>"></td>
                <td><input type="time" name="event_time" value="<?= $event['event_time'] ?>"></td>
                <td><input type="text" name="venue" value="<?= htmlspecialchars($event['venue']) ?>"></td>
                <td><input type="number" name="total_tickets" value="<?= $event['total_tickets'] ?>"></td>
                <td><?= $event['available_tickets'] ?></td>
                <td><input type="number" name="ticket_price" value="<?= $event['ticket_price'] ?>"></td>
                <td>
                    <input type="hidden" name="update_id" value="<?= $event['id'] ?>">
                    <input type="submit" value="Update">
                    <a class="delete-btn" href="../controller/EventController.php?delete_id=<?= $event['id'] ?>" onclick="return confirm('Delete this event?')">Delete</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
