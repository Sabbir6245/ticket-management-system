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
}

?>
