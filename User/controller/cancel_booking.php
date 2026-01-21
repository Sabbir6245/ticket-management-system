<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['booking_id'])) {
    header("Location: ../view/my_bookings.php");
    exit;
}

$booking_id = (int)$_POST['booking_id'];
$user_id = $_SESSION['id'] ?? 0;

$db = new DatabaseConnection();
$conn = $db->openConnection();


$stmt = $conn->prepare("SELECT event_id, tickets_booked FROM bookings WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1){
    $booking = $result->fetch_assoc();
    $event_id = $booking['event_id'];
    $tickets = $booking['tickets_booked'];

   
    $stmt2 = $conn->prepare("UPDATE events SET available_tickets = available_tickets + ? WHERE id=?");
    $stmt2->bind_param("ii", $tickets, $event_id);
    $stmt2->execute();

    
    $stmt3 = $conn->prepare("DELETE FROM bookings WHERE id=? AND user_id=?");
    $stmt3->bind_param("ii", $booking_id, $user_id);
    $stmt3->execute();

    $_SESSION['bookingMessage'] = "Booking cancelled successfully!";
} else {
    $_SESSION['bookingMessage'] = "Booking not found or you are not authorized.";
}

header("Location: ../view/my_bookings.php");
exit;
