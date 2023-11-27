<?php
session_start();
require_once('db.php');

// Retrieve user input
$id = $_POST['id'];
$password = $_POST['password'];

// SQL query to check user credentials
$sql = "SELECT * FROM vendor_user WHERE id = '$id' AND password = '$password'";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // Successful login
    session_start();
    $_SESSION['id'] = $id;
    header("Location: Vendor_Dashboard.php");
} else {
    // Invalid login
    echo "Invalid login credentials";
}
?>