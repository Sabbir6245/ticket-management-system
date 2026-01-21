<?php
session_start();

$event_id = $_POST['event_id'] ?? 0;
$current_status = $_POST['current_status'] ?? 'active';

$new_status = ($current_status === 'active') ? 'muted' : 'active';

$conn = new mysqli("localhost", "root", "", "ticket_management");

$stmt = $conn->prepare("UPDATE events SET status=? WHERE id=?");
$stmt->bind_param("si", $new_status, $event_id);
$stmt->execute();

$conn->close();

echo "success";
?>