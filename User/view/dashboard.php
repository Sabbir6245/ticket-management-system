<?php
session_start();

if (!($_SESSION['isLoggedIn'] ?? false) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'] ?? '';

$conn = new mysqli("localhost", "root", "", "ticket_management");
if ($conn->connect_error) {
    die("DB connection failed");
}

$stmt = $conn->prepare("SELECT id, username, email, profile_pic FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found");
}

$userId = $user['id'];


if (isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {

        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $newName = "user_" . $userId . "_" . time() . "." . $ext;
            $dir = "../uploads/profile/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dir.$newName);

            $up = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $up->bind_param("si", $newName, $userId);
            $up->execute();

            header("Location: dashboard.php");
            exit;
        }
    }
}

$profileImg = $user['profile_pic']
    ? "../uploads/profile/".$user['profile_pic']
    : "../uploads/profile/default.png";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>
<style>
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#1abc9c,#16a085);
    min-height:100vh;
}
.dashboard{
    width:420px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:8px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
    text-align:center;
}
.profile img{
    width:100px;
    height:100px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #1abc9c;
}
.profile form{
    margin-top:10px;
}
.profile input[type=file]{
    font-size:12px;
}
.profile button{
    padding:6px 12px;
    background:#27ae60;
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
}
.profile button:hover{ background:#229954; }

h2{ margin-top:15px; color:#2c3e50; }
p{ color:#555; font-size:14px; }

a.btn{
    display:block;
    padding:12px;
    margin:10px 0;
    background:#1abc9c;
    color:#fff;
    text-decoration:none;
    border-radius:4px;
}
a.btn:hover{ background:#159a80; }

a.logout{
    background:#e74c3c;
}
a.logout:hover{ background:#c0392b; }

#bookingBox{
    margin-top:20px;
    text-align:left;
}
.booking{
    background:#f2f2f2;
    padding:10px;
    border-radius:4px;
    margin-bottom:8px;
    font-size:13px;
}
</style>
</head>

<body>

<div class="dashboard">

    <div class="profile">
        <img src="<?= $profileImg ?>">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button name="upload_photo">Upload</button>
        </form>
    </div>

    <h2>User Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($user['username']) ?></p>

    <a class="btn" href="events.php">Upcoming Events</a>
    <a class="btn" href="my_bookings.php">My Bookings</a>
    <a class="btn logout" href="../controller/logout.php">Logout</a>

    <div id="bookingBox"></div>

</div>

<script>
function loadBookings(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState === 4 && this.status === 200){
            document.getElementById("bookingBox").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET","../controller/fetch_user_bookings.php",true);
    xhttp.send();
}

loadBookings();
</script>

</body>
</html>
