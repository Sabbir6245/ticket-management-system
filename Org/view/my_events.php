<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Events</title>
    <script src="../controller/JS/events.js"></script>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 6px; }
        h2 { text-align: center; color: #2c3e50; }
        
        .home-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #95a5a6;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .home-btn:hover {
            background: #7f8c8d;
        }
        
        .create-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #27ae60;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .create-btn:hover {
            background: #229954;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f2f2f2; }
        
        #message {
            display: none;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            color: green;
            background: #d4edda;
        }
        .error {
            color: red;
            background: #f8d7da;
        }
        
        input[type="text"], input[type="date"], input[type="time"], input[type="number"] { 
            width: 100%; 
            padding: 5px; 
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        
        .update-btn { 
            padding: 5px 10px; 
            background: #3498db; 
            color: #fff; 
            border: none; 
            border-radius: 3px; 
            cursor: pointer; 
        }
        .update-btn:hover { 
            background: #2980b9; 
        }
        
        .delete-btn { 
            padding: 5px 10px; 
            background: #e74c3c; 
            color: #fff; 
            border: none; 
            border-radius: 3px; 
            cursor: pointer;
            margin-left: 5px;
        }
        .delete-btn:hover { 
            background: #c0392b; 
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Events</h2>
    
    <a href="dashboard.php" class="home-btn"> Home</a>
    
    <div id="message"></div>

    <a href="create_event.php" class="create-btn"> Create Event</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Total Tickets</th>
                <th>Available Tickets</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="events-body">
            <tr>
                <td colspan="10" style="text-align:center;">Loading events...</td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>