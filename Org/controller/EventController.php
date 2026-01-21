<?php
session_start();
include "../model/DatabaseConnection.php";

$organiser_id = $_SESSION['id'] ?? 0; 

$db = new DatabaseConnection();
$conn = $db->openConnection();


if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=? AND organiser_id=?");
    $stmt->bind_param("ii", $id, $organiser_id);
    $stmt->execute();
    $_SESSION['eventSuccess'] = "Event deleted successfully!";
    header("Location: ../view/my_events.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $venue = $_POST['venue'];
    $total = (int)$_POST['total_tickets'];
    $price = (int)$_POST['ticket_price'];

    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, venue=?, total_tickets=?, ticket_price=? WHERE id=? AND organiser_id=?");
    $stmt->bind_param("sssssiiii", $title, $description, $date, $time, $venue, $total, $price, $id, $organiser_id);
    $stmt->execute();

    $_SESSION['eventSuccess'] = "Event updated successfully!";
    header("Location: ../view/my_events.php");
    exit;
}


if(isset($_POST['create'])){
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $total_tickets = (int)($_POST['total_tickets'] ?? 0);
    $ticket_price = (int)($_POST['ticket_price'] ?? 0);

    if(!$title || !$description || !$event_date || !$event_time || !$venue || $total_tickets <=0 || $ticket_price < 0){
        $_SESSION['eventError'] = "Please fill all fields correctly.";
        header("Location: ../view/create_event.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO events (organiser_id, title, description, event_date, event_time, venue, total_tickets, available_tickets, ticket_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssiii", $organiser_id, $title, $description, $event_date, $event_time, $venue, $total_tickets, $total_tickets, $ticket_price);

    if($stmt->execute()){
        $_SESSION['eventSuccess'] = "Event created successfully!";
        header("Location: ../view/my_events.php");
    } else {
        $_SESSION['eventError'] = "Failed to create event.";
        header("Location: ../view/create_event.php");
    }
    exit;
}
?>