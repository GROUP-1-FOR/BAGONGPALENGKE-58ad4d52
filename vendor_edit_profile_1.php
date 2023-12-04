<?php
require("config.php");
//Error message for the first part of the form
$vendor_first_name_error  = $vendor_last_name_error  = $vendor_mobile_number_error = $vendor_email_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Part 1 variables
    $vendor_first_name = isset($_POST["vendor_first_name"]) ? htmlspecialchars($_POST["vendor_first_name"]) : '';
    $vendor_last_name = isset($_POST["vendor_last_name"]) ? htmlspecialchars($_POST["vendor_last_name"]) : '';
    $vendor_mobile_number = isset($_POST["vendor_mobile_number"]) ? trim(htmlspecialchars($_POST["vendor_mobile_number"])) : '';
    $vendor_email = isset($_POST["vendor_email"]) ? htmlspecialchars($_POST["vendor_email"]) : '';
    $vendor_product_type = isset($_POST["vendor_product"]) ? htmlspecialchars($_POST["vendor_product"]) : '';

    // Fetch vendor_userid from session (Assuming you store it during login)
    $vendor_userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';

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
        header("Location: vendor_edit_profile.php");
        exit();
    }


    // Perform database insertion (you might need to adjust table/column names)
    $sql = "INSERT INTO vendor_edit_profile (vendor_userid, vendor_first_name, vendor_last_name, vendor_name, vendor_mobile_number, vendor_product, vendor_email) 
    VALUES ('$vendor_userid', '$vendor_first_name', '$vendor_last_name', '$vendor_full_name', '$vendor_mobile_number', '$vendor_product_type', '$vendor_email')";
    if ($connect->query($sql) === TRUE) {
        echo '<script>';
        echo 'alert("Vendor Account Update On Process!");';
        echo 'window.location.href = "vendor_index.php";';
        echo '</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $connect->error;
    }

    // Close the database connection
    $connect->close();
}
