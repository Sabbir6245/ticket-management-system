<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$organiser_id = $_SESSION['id'];
$db = new DatabaseConnection();
$conn = $db->openConnection();

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $id = $_POST['id'] ?? 0;
    
    $stmt = $conn->prepare("DELETE FROM events WHERE id=? AND organiser_id=?");
    $stmt->bind_param("ii", $id, $organiser_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed']);
    }
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['event_date'] ?? '';
    $time = $_POST['event_time'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $total = (int)($_POST['total_tickets'] ?? 0);
    $price = (int)($_POST['ticket_price'] ?? 0);
    
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, venue=?, total_tickets=?, ticket_price=? WHERE id=? AND organiser_id=?");
    $stmt->bind_param("sssssiiii", $title, $description, $date, $time, $venue, $total, $price, $id, $organiser_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
    exit;
}

if ($action === 'fetch') {
    $stmt = $conn->prepare("SELECT * FROM events WHERE organiser_id=? ORDER BY event_date DESC");
    $stmt->bind_param("i", $organiser_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['success' => true, 'events' => $events]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>