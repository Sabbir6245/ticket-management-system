<?php

class DatabaseConnection {

    function openConnection() {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "ticket_management";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

        if ($connection->connect_error) {
            die("Failed to connect database " . $connection->connect_error);
        }

        return $connection;
    }

  
    function signup($connection, $tableName, $username, $email, $password, $phone, $address, $role) {

        $sql = "INSERT INTO " . $tableName . " 
                (username, email, password, phone, address, role)
                VALUES (
                    '" . $username . "',
                    '" . $email . "',
                    '" . $password . "',
                    '" . $phone . "',
                    '" . $address . "',
                    '" . $role . "'
                )";

        $result = $connection->query($sql);

        if (!$result) {
            return false;
        }

        return true;
    }

  
    function signin($connection, $tableName, $email, $password) {

        $sql = "SELECT * FROM " . $tableName . "
                WHERE email='" . $email . "'
                AND password='" . $password . "'";

        $result = $connection->query($sql);
        return $result;
    }

    function closeConnection($connection) {
        $connection->close();
    }

   
public function getAllEvents($connection) {
    return $connection->query("SELECT * FROM events WHERE status='active' AND available_tickets>0");
}


public function bookEvent($connection, $user_id, $event_id, $tickets, $total_price) {
    $stmt = $connection->prepare("INSERT INTO bookings (user_id, event_id, tickets_booked, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $event_id, $tickets, $total_price);
    if($stmt->execute()){
        $connection->query("UPDATE events SET available_tickets = available_tickets - $tickets WHERE id=$event_id");
        return true;
    }
    return false;
}


public function getUserBookings($connection, $user_id) {
    return $connection->query("
        SELECT b.id, e.title, b.tickets_booked, b.total_price, b.booked_at
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id=$user_id
    ");
}

}

?>
