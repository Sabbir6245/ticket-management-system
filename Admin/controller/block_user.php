<?php
session_start();

$user_id = $_POST['user_id'] ?? 0;
$current_status = $_POST['current_status'] ?? 'active';

$new_status = ($current_status === 'active') ? 'blocked' : 'active';

$conn = new mysqli("localhost", "root", "", "ticket_management");

$stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
$stmt->bind_param("si", $new_status, $user_id);
$stmt->execute();

$conn->close();

echo "success";
?>