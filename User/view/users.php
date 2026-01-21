<?php
session_start();
if (!($_SESSION['isLoggedIn'] ?? false)) {
    header("Location: login.php");
    exit;
}

include "../controller/UserController.php";
?>

<html>
<head>
    <script src="../js/userStatus.js"></script>
    <title>Admin - Users</title>
    <style>
        body { font-family: Arial; }
        table { border-collapse: collapse; width: 60%; margin: auto; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2 align="center">All Users</h2>

<table>
<tr>
    <th>ID</th>
    <th>Email</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $users->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['email'] ?></td>
    <td id="status-<?= $row['id'] ?>">
        <?= $row['status'] ?>
    </td>
    <td>
        <?php if($row['status'] == 'active'): ?>
            <button onclick="updateStatus(<?= $row['id'] ?>, 'blocked')">
                Block
            </button>
        <?php else: ?>
            <button onclick="updateStatus(<?= $row['id'] ?>, 'active')">
                Unblock
            </button>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>


</body>
</html>
