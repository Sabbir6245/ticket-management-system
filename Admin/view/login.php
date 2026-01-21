<?php
session_start();

$isLoggedIn = $_SESSION["isLoggedIn"] ?? false;
if ($isLoggedIn) {
    header("Location: dashboard.php");
    exit;
}

$emailErr =  $_SESSION["emailErr"] ?? '';
$passErr = $_SESSION['passwordErr'] ?? '';
$previousValues = $_SESSION['previousValues'] ?? [];
$loginErr = $_SESSION["LoginErr"] ?? "";

unset($_SESSION['previousValues']);
unset($_SESSION["LoginErr"]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .login-box {
            width: 300px;
            margin: 80px auto;
            padding: 20px;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
        }

        td {
            padding: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 95%;
            padding: 6px;
            border: 1px solid #aaa;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 8px 16px;
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #000;
        }

        .error {
            color: red;
            font-size: 0.85em;
        }
      body { background: url("../uploads/bglogin.png") no-repeat center center fixed; background-size: cover; }

    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
    <form method="post" action="../controller/loginauth.php">
        <table>
            <tr>
                <td>Email</td>
                <td>
                    <input type="text" name="email" id="email" value="<?php echo $previousValues["email"] ?? '' ?>"/>
                </td>
                <td class="error"><?php echo $emailErr; ?></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>
                    <input type="password" id="password" name="password"/>
                </td>
                <td class="error"><?php echo $passErr; ?></td>
            </tr>
            <tr>
                <td colspan="2" class="error"><?php echo $loginErr; ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="login" value="Login"/>
                </td>
            </tr>
        </table>
    </form>
</div>

</body>
</html>
