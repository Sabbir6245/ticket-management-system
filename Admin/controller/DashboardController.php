<?php
session_start();
include "../model/DatabaseConnection.php";


$db = new DatabaseConnection();
$conn = $db->openConnection();

$model = new AdminModel();
$counts = $model->getDashboardCounts($conn);

$db->closeConnection($conn);

include "../Admin/view/dashboard.php";
