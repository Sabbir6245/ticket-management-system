<?php
include "../model/DatabaseConnection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
    if (!($_SESSION['isLoggedIn'] ?? false) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo "error";

    exit;
    }

    

    $userId = $_POST["id"];
    $status = $_POST["status"];

    $db = new DatabaseConnection();
    $conn = $db->openConnection();

    $sql = "UPDATE users SET status='$status' WHERE id=$userId";

    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error";
    }

    $db->closeConnection($conn);
}
