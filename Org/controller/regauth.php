<?php
session_start();
include "../model/DatabaseConnection.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$email    = $_POST['email'] ?? '';
$phone    = $_POST['phone'] ?? '';
$address  = $_POST['address'] ?? '';
$role     = $_POST['role'] ?? '';

$errors = [];
$values = compact('username','email','phone','address','role');

// Validation
if(!$username) $errors['username'] = "Username is required";
if(!$password) $errors['password'] = "Password is required";
if(!$email)    $errors['email']    = "Email is required";
if(!$phone)    $errors['phone']    = "Phone number is required";
if(!$address)  $errors['address']  = "Address is required";
if(!$role)     $errors['role']     = "Role is required";

if(count($errors) > 0){
    $_SESSION['usernameErr'] = $errors['username'] ?? '';
    $_SESSION['passwordErr'] = $errors['password'] ?? '';
    $_SESSION['emailErr']    = $errors['email'] ?? '';
    $_SESSION['phoneErr']    = $errors['phone'] ?? '';
    $_SESSION['addressErr']  = $errors['address'] ?? '';
    $_SESSION['roleErr']     = $errors['role'] ?? '';
    $_SESSION['previousValues'] = $values;
    header("Location: ../view/reg.php");
    exit;
}

$password_hashed = password_hash($password, PASSWORD_DEFAULT);

$db = new DatabaseConnection();
$conn = $db->openConnection();

$result = $db->signup($conn, 'users', $username, $email, $password_hashed, $phone, $address, $role);

if($result){
    header("Location: ../view/login.php");
    exit;
}else{
    $_SESSION['RegisterErr'] = "Registration failed. Please try again.";
    $_SESSION['previousValues'] = $values;
    header("Location: ../view/reg.php");
    exit;
}

?>