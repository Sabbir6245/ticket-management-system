<?php 
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? false;
if (!$isLoggedIn) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION["email"] ?? "";

$conn = new mysqli("localhost", "root", "", "ticket_management");
if ($conn->connect_error) {
    die("Database connection failed");
}

$stmt = $conn->prepare("SELECT id, username, email, profile_pic FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    die("Admin not found");
}

$userId = $admin['id'];
$message = "";

if (isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $newName = "admin_" . $userId . "_" . time() . "." . $ext;
            $dir = "../uploads/profile/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dir.$newName);

            $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $stmt->bind_param("si", $newName, $userId);
            $stmt->execute();
            
            header("Location: dashboard.php");
            exit;
        }
    }
}

$profileImg = $admin['profile_pic']
    ? "../uploads/profile/".$admin['profile_pic']
    : "../uploads/profile/default.png";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
body { 
    font-family: Arial; 
    background: url("../uploads/bgadmin.jpg") no-repeat center center fixed; 
    background-size: cover; 
}
.box { 
    width:90%; 
    max-width:1000px; 
    margin:30px auto; 
    background:#fff; 
    padding:20px; 
    border-radius:8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
table { width:100%; border-collapse:collapse; margin-bottom:30px; }
th,td { border:1px solid #aaa; padding:8px; text-align:left; }
th { background:#333; color:#fff; }
tr:nth-child(even){ background:#f2f2f2; }
.logout { 
    background:#e74c3c; 
    color:#fff; 
    padding:8px 15px; 
    text-decoration:none; 
    border-radius:4px;
    display:inline-block;
    margin-bottom:20px;
}
.logout:hover { background:#c0392b; }
.profile { 
    display:flex; 
    gap:20px; 
    align-items:center; 
    margin-bottom:20px; 
    padding:15px;
    background:#f8f9fa;
    border-radius:8px;
}
.profile img { 
    width:90px; 
    height:90px; 
    border-radius:50%; 
    object-fit:cover; 
    border:3px solid #3498db; 
}
.profile form { margin-top:10px; }
.profile input[type="file"] { 
    padding:5px; 
    border:1px solid #ddd;
    border-radius:4px;
    margin-right:5px;
}
.profile button { 
    padding:6px 12px; 
    background:#27ae60; 
    color:#fff; 
    border:none;
    border-radius:4px;
    cursor:pointer;
}
.profile button:hover { background:#229954; }

.block-btn { 
    padding:5px 10px; 
    background:#e74c3c; 
    color:#fff; 
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
}
.block-btn:hover { background:#c0392b; }

.unblock-btn { 
    padding:5px 10px; 
    background:#27ae60; 
    color:#fff; 
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
}
.unblock-btn:hover { background:#229954; }

.mute-btn { 
    padding:5px 10px; 
    background:#f39c12; 
    color:#fff; 
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
}
.mute-btn:hover { background:#e67e22; }

.unmute-btn { 
    padding:5px 10px; 
    background:#3498db; 
    color:#fff; 
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-size:12px;
}
.unmute-btn:hover { background:#2c80b4; }

.status-active { color:#27ae60; font-weight:bold; }
.status-blocked { color:#e74c3c; font-weight:bold; }
.status-muted { color:#f39c12; font-weight:bold; }

h3 { color:#2c3e50; border-bottom:2px solid #3498db; padding-bottom:5px; }
</style>
</head>

<body>
<div class="box">

<h2>Admin Dashboard</h2>

<div class="profile">
    <img src="<?= $profileImg ?>" alt="Profile">
    <div>
        <strong><?= htmlspecialchars($admin['username']) ?></strong><br>
        <?= htmlspecialchars($admin['email']) ?>

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button type="submit" name="upload_photo">Upload Photo</button>
        </form>
    </div>
</div>

<a class="logout" href="../controller/logout.php">Logout</a>

<h3>All Users</h3>
<div id="users-table"></div>

<h3>All Events</h3>
<div id="events-table"></div>

<h3>User Summary</h3>
<div id="summary-table"></div>

</div>

<script>
function loadData() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            
            document.getElementById("users-table").innerHTML = data.users;
            document.getElementById("events-table").innerHTML = data.events;
            document.getElementById("summary-table").innerHTML = data.summary;
        }
    };
    xhttp.open("GET", "../controller/fetch_dashboard_data.php", true);
    xhttp.send();
}

function blockUser(userId, currentStatus) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            loadData();
        }
    };
    xhttp.open("POST", "../controller/block_user.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("user_id=" + userId + "&current_status=" + currentStatus);
}

function muteEvent(eventId, currentStatus) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            loadData();
        }
    };
    xhttp.open("POST", "../controller/mute_event.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("event_id=" + eventId + "&current_status=" + currentStatus);
}

loadData();
setInterval(loadData, 5000);
</script>

</body>
</html>