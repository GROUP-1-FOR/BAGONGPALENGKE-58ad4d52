<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 1 variables
    $vendor_name = isset($_POST["vendor_name"]) ? htmlspecialchars($_POST["vendor_name"]) : '';
    $vendor_stall_number = isset($_POST["vendor_stall_number"]) ? htmlspecialchars($_POST["vendor_stall_number"]) : '';
    $vendor_mobile_number = isset($_POST["vendor_mobile_number"]) ? htmlspecialchars($_POST["vendor_mobile_number"]) : '';
    $vendor_email = isset($_POST["vendor_email"]) ? htmlspecialchars($_POST["vendor_email"]) : '';
    $vendor_product_type = isset($_POST["vendor_product"]) ? htmlspecialchars($_POST["vendor_product"]) : '';

    // Step 2 variables
    $vendor_userid = isset($_POST["vendor_userid"]) ? htmlspecialchars($_POST["vendor_userid"]) : '';
    $vendor_password = isset($_POST["vendor_password"]) ? htmlspecialchars($_POST["vendor_password"]) : '';
    $vendor_confirm_password = isset($_POST["vendor_confirm_password"]) ? htmlspecialchars($_POST["vendor_confirm_password"]) : '';


    if ($vendor_password !== $vendor_confirm_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "/admin_create_vendor_account.php";';
        echo '</script>';
        exit();
    }

    // Hash the password
    $hashedPassword = md5($vendor_password);

    // Check if the account already exists
    $checkIfExists = "SELECT * FROM vendor_sign_in WHERE vendor_userid = '$vendor_userid'";
    $resultIfExists = $connect->query($checkIfExists);

    if ($resultIfExists->num_rows > 0) {
        echo '<script>';
        echo 'alert("Account already exists for this user ID!");';
        echo 'window.location.href = "/admin_create_vendor_account.php";';
        echo '</script>';
        exit();
    }

    // Perform database insertion (you might need to adjust table/column names)
    $sql = "INSERT INTO vendor_sign_in (vendor_name, vendor_stall_number,vendor_mobile_number,vendor_product,vendor_email, vendor_userid, vendor_password) 
        VALUES ('$vendor_name', '$vendor_stall_number','$vendor_mobile_number','$vendor_product_type','$vendor_email', '$vendor_userid', '$hashedPassword')";

    if ($connect->query($sql) === TRUE) {
        echo '<script>';
        echo 'alert("Vendor Account Created Successfully!");';
        echo 'window.location.href = "admin_index.php";';
        echo '</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $connect->error;
    }

    // Close the database connection
    $connect->close();
}
