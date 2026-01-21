<?php
session_start();
include "../model/DatabaseConnection.php";


if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 
$db = new DatabaseConnection();
$conn = $db->openConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_event'])) {
    $event_id = (int)($_POST['event_id'] ?? 0);
    $tickets  = (int)($_POST['tickets'] ?? 0);

    
    $eventResult = $conn->query("SELECT * FROM events WHERE id=$event_id AND status='active'");
    $event = $eventResult->fetch_assoc();

    if (!$event) {
        $_SESSION['bookingError'] = "Event not found or inactive.";
        header("Location: ../view/events.php");
        exit;
    }

    if ($tickets <= 0 || $tickets > $event['available_tickets']) {
        $_SESSION['bookingError'] = "Invalid number of tickets.";
        header("Location: ../view/events.php");
        exit;
    }

    $total_price = $tickets * $event['ticket_price'];

   
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, tickets_booked, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $event_id, $tickets, $total_price);

    if ($stmt->execute()) {
       
        $conn->query("UPDATE events SET available_tickets = available_tickets - $tickets WHERE id=$event_id");
        $_SESSION['bookingSuccess'] = "Successfully booked $tickets tickets for '{$event['title']}'!";
    } else {
        $_SESSION['bookingError'] = "Booking failed. Please try again.";
    }

    header("Location: ../view/events.php");
    exit;
}
?>
