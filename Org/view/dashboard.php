<?php
session_start();
include "../model/DatabaseConnection.php";

if (!($_SESSION['isLoggedIn'] ?? false) || $_SESSION['role'] !== 'event_org') {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email']??''; 
$user_id = $_SESSION['id']??0;

$db = new DatabaseConnection();
$conn = $db->openConnection();

$uploadMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $upload_dir = "../uploads/profiles/";
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if ($_FILES['profile_pic']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        
        if (in_array($_FILES['profile_pic']['type'], $allowed) && $_FILES['profile_pic']['size'] < 5000000) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = "profile_" . $user_id . "_" . time() . "." . $ext;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $filename)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->bind_param("si", $filename, $user_id);
                $stmt->execute();
                $uploadMsg = "Profile picture updated!";
            }
        } else {
            $uploadMsg = "Invalid file type or size too large (max 5MB)";
        }
    }
}

$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_pic = $user['profile_pic'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organizer Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            min-height: 100vh;
            padding: 20px 0;
        }

        .dashboard {
            width: 420px;
            background: #ffffff;
            padding: 30px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3498db;
            background: #ecf0f1;
            margin-bottom: 15px;
        }

        .file-input {
            display: none;
        }

        .upload-btn {
            display: inline-block;
            padding: 6px 12px;
            background: #27ae60;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .upload-btn:hover {
            background: #229954;
        }

        .msg {
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            font-size: 13px;
            background: #d4edda;
            color: #155724;
        }

        .dashboard h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .dashboard p {
            margin-bottom: 25px;
            color: #555;
            font-size: 14px;
        }

        .dashboard a {
            display: block;
            padding: 12px;
            margin: 10px 0;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 15px;
        }

        .dashboard a:hover {
            background: #2c80b4;
        }

        .dashboard a.logout {
            background: #e74c3c;
        }

        .dashboard a.logout:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <?php if($profile_pic && file_exists("../uploads/profiles/" . $profile_pic)): ?>
        <img src="../uploads/profiles/<?= htmlspecialchars($profile_pic) ?>" class="profile-pic">
    <?php else: ?>
        <img src="../uploads/profiles/default.png" class="profile-pic" >
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" style="margin-bottom:20px;">
        <label for="pic" class="upload-btn">Change Photo</label>
        <input type="file" name="profile_pic" id="pic" class="file-input" accept="image/*" onchange="this.form.submit()">
    </form>
    
    <?php if($uploadMsg): ?>
        <div class="msg"><?= htmlspecialchars($uploadMsg) ?></div>
    <?php endif; ?>

    <h2>Event Organizer</h2>
    <p>Welcome, <?= htmlspecialchars($email) ?></p>

    <a href="create_event.php">Create Event</a>
    <a href="my_events.php">My Events</a>
    <a href="../controller/logout.php" class="logout">Logout</a>
</div>

</body>
</html>