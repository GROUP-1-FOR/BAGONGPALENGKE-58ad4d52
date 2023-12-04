<?php
require("config.php");
//Error message for the first part of the form
$vendor_first_name_error  = $vendor_last_name_error  = $vendor_mobile_number_error = $vendor_email_error = "";
//Error message for the second part
$vendor_userid_error = $vendor_password_error = $vendor_confirm_password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Part 1 variables
    $vendor_first_name = isset($_POST["vendor_first_name"]) ? htmlspecialchars($_POST["vendor_first_name"]) : '';
    $vendor_last_name = isset($_POST["vendor_last_name"]) ? htmlspecialchars($_POST["vendor_last_name"]) : '';
    $vendor_stall_number = isset($_POST["vendor_stall_number"]) ? trim(htmlspecialchars($_POST["vendor_stall_number"])) : '';
    $vendor_mobile_number = isset($_POST["vendor_mobile_number"]) ? trim(htmlspecialchars($_POST["vendor_mobile_number"])) : '';
    $vendor_email = isset($_POST["vendor_email"]) ? htmlspecialchars($_POST["vendor_email"]) : '';
    $vendor_product_type = isset($_POST["vendor_product"]) ? htmlspecialchars($_POST["vendor_product"]) : '';

    if (!preg_match("/^[a-zA-Z-' ]*$/", $vendor_first_name)) {
        $vendor_first_name_error = "Only letters are allowed";
        $_SESSION['vendor_first_name_error'] = $vendor_first_name_error;
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/", $vendor_last_name)) {
        $vendor_last_name_error = "Only letters are allowed";
        $_SESSION['vendor_last_name_error'] = $vendor_last_name_error;
    }

    if (!is_numeric($vendor_mobile_number)) {
        $vendor_mobile_number_error = "Only numbers are allowed";
        $_SESSION['vendor_mobile_number_error'] = $vendor_mobile_number_error;
    }
    if (!filter_var($vendor_email, FILTER_VALIDATE_EMAIL)) {
        $vendor_email_error = "Wrong email format";
        $_SESSION['vendor_email_error'] = $vendor_email_error;
    }

    if (isset($_SESSION['vendor_first_name_error']) || isset($_SESSION['vendor_last_name_error']) || isset($_SESSION['vendor_mobile_number_error']) || isset($_SESSION['vendor_email_error'])) {
        header("Location: admin_create_vendor_account.php");
        exit();
    }


    // Part2 variables
    $vendor_userid = isset($_POST["vendor_userid"]) ? htmlspecialchars($_POST["vendor_userid"]) : '';
    $vendor_password = isset($_POST["vendor_password"]) ? htmlspecialchars($_POST["vendor_password"]) : '';
    $vendor_confirm_password = isset($_POST["vendor_confirm_password"]) ? htmlspecialchars($_POST["vendor_confirm_password"]) : '';


    if ($vendor_password !== $vendor_confirm_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "admin_create_vendor_account.php";';
        echo '</script>';
        exit();
    }

    // Hash the password
    $hashedPassword = md5($vendor_password);
    $vendor_full_name = $vendor_first_name . " " . $vendor_last_name;

    // Check if the account already exists
    $checkIfExists = "SELECT * FROM vendor_sign_in WHERE vendor_userid = '$vendor_userid'";
    $resultIfExists = $connect->query($checkIfExists);

    if ($resultIfExists->num_rows > 0) {
        echo '<script>';
        echo 'alert("Account already exists for this user ID!");';
        echo 'window.location.href = "admin_create_vendor_account.php";';
        echo '</script>';
        exit();
    }

    // Perform database insertion (you might need to adjust table/column names)
    $sql = "INSERT INTO vendor_sign_in (vendor_first_name, vendor_last_name, vendor_name, vendor_stall_number,vendor_mobile_number,vendor_product,vendor_email, vendor_userid, vendor_password, balance) 
        VALUES ('$vendor_first_name','$vendor_last_name','$vendor_full_name', '$vendor_stall_number','$vendor_mobile_number','$vendor_product_type','$vendor_email', '$vendor_userid', '$hashedPassword', '0.00')";

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
