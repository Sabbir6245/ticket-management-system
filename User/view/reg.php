<?php
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? false;
if ($isLoggedIn) {
    header("Location: login.php");
    exit;
}

$usernameErr = $_SESSION["usernameErr"] ?? "";
$passwordErr = $_SESSION["passwordErr"] ?? "";
$emailErr    = $_SESSION["emailErr"] ?? "";
$phoneErr      = $_SESSION["phoneErr"] ?? "";
$addressErr  = $_SESSION["addressErr"] ?? "";
$roleErr     = $_SESSION["roleErr"] ?? "";
$registerErr = $_SESSION["RegisterErr"] ?? "";

$previousValues = $_SESSION["previousValues"] ?? [];

unset($_SESSION["usernameErr"], $_SESSION["passwordErr"], $_SESSION["emailErr"]);
unset($_SESSION["phoneErr"], $_SESSION["addressErr"], $_SESSION["roleErr"], $_SESSION["RegisterErr"]);
unset($_SESSION["previousValues"]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .form-container {
            width: 350px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 7px;
            margin-top: 5px;
            border: 1px solid #aaa;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-top: 15px;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #000;
        }

        .error {
            color: red;
            font-size: 0.85em;
        }

        .register-error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Register</h2>
    <form method="post" action="../controller/regauth.php">

        <label>Username
            <input type="text" name="username" value="<?php echo $previousValues['username'] ?? ''; ?>">
            <div class="error"><?php echo $usernameErr; ?></div>
        </label>

        <label>Password
            <input type="password" name="password">
            <div class="error"><?php echo $passwordErr; ?></div>
        </label>

        <label>Email
            <input type="text" name="email" value="<?php echo $previousValues['email'] ?? ''; ?>">
            <div class="error"><?php echo $emailErr; ?></div>
        </label>

        <label>Phone Number
            <input type="text" name="phone" value="<?php echo $previousValues['phone'] ?? ''; ?>">
            <div class="error"><?php echo $phoneErr; ?></div>
        </label>

        <label>Address
            <input type="text" name="address" value="<?php echo $previousValues['add'] ?? ''; ?>">
            <div class="error"><?php echo $addressErr; ?></div>
        </label>

        <label>Role
            <select name="role">
                <option value="">Select</option>
                <option value="user" <?php if (($previousValues['role'] ?? '') == 'user') echo 'selected'; ?>>User</option>
                <option value="event_org" <?php if (($previousValues['role'] ?? '') == 'event_org') echo 'selected'; ?>>Event Organizer</option>
                <option value="admin" <?php if (($previousValues['role'] ?? '') == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <div class="error"><?php echo $roleErr; ?></div>
        </label>

        <input type="submit" name="submit" value="Register">
        <div class="register-error"><?php echo $registerErr; ?></div>

    </form>
</div>

</body>
</html>
