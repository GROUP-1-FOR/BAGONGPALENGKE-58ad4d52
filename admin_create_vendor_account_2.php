<?php
require("config.php");

//MAY CHANGES DITO PINASTE KO NA LANG WALA KANG GAGALAWIN IPAPASTE MO LANG SA LOCAL MO ITONG CODE NA NTO

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
    } else {
        header("location:admin_logout.php");
    }

    // Initialize the password confirmation trial count if not set
    if (!isset($_SESSION['admin_password_confirmation_trial'])) {
        $_SESSION['admin_password_confirmation_trial'] = 0;
    }

    $admin_password_confirmation_trial = $_SESSION['admin_password_confirmation_trial'];

    // Retrieve vendor information from session variables
    $vendor_first_name = $_SESSION['vendor_first_name'];
    $vendor_last_name =  $_SESSION['vendor_last_name'];
    $vendor_full_name = $_SESSION['vendor_full_name'];
    $vendor_stall_number = $_SESSION['vendor_stall_number'];
    $vendor_mobile_number = $_SESSION['vendor_mobile_number'];
    $vendor_email = $_SESSION['vendor_email'];
    $vendor_product_type = $_SESSION['vendor_product_type'];
    // $vendor_payment_basis = $_SESSION['vendor_payment_basis'];
    $vendor_first_payment_date = $_SESSION['vendor_first_payment_date'];
    $vendor_userid = $_SESSION['vendor_userid'];
    $vendor_password = $_SESSION['vendor_hashed_password'];
    $vendor_transaction_id = $_SESSION['vendor_transaction_id'];

    $first_payment_date_breakdown = explode('-', $vendor_first_payment_date);
    $year = intval($first_payment_date_breakdown[0]);
    $month = intval($first_payment_date_breakdown[1]);
    $day = intval($first_payment_date_breakdown[2]);

    $balanceValues = [
        'Wet' => 1100.51,
        'Dry' => 3200.30,
        'Other' => 5800.50,
    ];

    $remaining_balance = $balanceValues[$vendor_product_type];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $admin_password = htmlspecialchars($_POST["admin_password"]);

        // Query the database for admin information
        $result = mysqli_query($connect, "SELECT * FROM admin_sign_in WHERE admin_userid= '$admin_userid'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            if (password_verify($admin_password, $row["admin_password"])) {
                // Insert data into vendor_sign_in table
                $sql1 = "INSERT INTO vendor_sign_in (vendor_first_name, vendor_last_name, vendor_name, vendor_stall_number, vendor_mobile_number, vendor_product, vendor_payment_basis, vendor_email, vendor_userid, vendor_password) 
                VALUES ('$vendor_first_name', '$vendor_last_name', '$vendor_full_name', '$vendor_stall_number', '$vendor_mobile_number', '$vendor_product_type', 'Monthly','$vendor_email', '$vendor_userid', '$vendor_password')";

                // Insert data into vendor_balance table
                $sql2 = "INSERT INTO vendor_balance (vendor_name, vendor_stall_number, vendor_product, vendor_userid, starting_date, balance, transaction_id, vendor_payment_basis, remaining_balance, month, day, year) 
            VALUES ('$vendor_full_name', '$vendor_stall_number', '$vendor_product_type', '$vendor_userid','$vendor_first_payment_date', 0.00 , '$vendor_transaction_id', 'Monthly', '$remaining_balance', '$month','$day','$year')";

                // Insert data into admin_stall_map table
                $sql3 = "INSERT INTO admin_stall_map (vendor_stall_number, vendor_name, vendor_userid, balance, vendor_payment_basis, vacant) 
                VALUES ('$vendor_stall_number', '$vendor_full_name', '$vendor_userid', '0.00', 'Monthly', '1')";

                // Execute SQL queries
                if ($connect->query($sql1) === TRUE && $connect->query($sql2) === TRUE && $connect->query($sql3) === TRUE) {
                    // All insertions successful
                    echo '<script>';
                    echo 'alert("Vendor Account Created Successfully!");';
                    echo 'window.location.href = "admin_index.php";';
                    echo '</script>';

                    // Clear session variables
                    unsetVendorSessionVariables();
                } else {
                    unsetVendorSessionVariables();
                    // If any insertion fails, display an error
                    echo "Error: " . $connect->error;
                }

                // Close the database connection
                $connect->close();
            } else {
                handleIncorrectCredentials();
            }
        } else {
            unsetVendorSessionVariables();
            echo '<script>';
            echo 'alert("No admin Found!");';
            echo 'window.location.href = "admin_index.php";';
            echo '</script>';
        }
    }

// Function to handle incorrect credentials
function handleIncorrectCredentials()
{
    // Increment the password confirmation trial count
    $_SESSION['admin_password_confirmation_trial']++;

    // Check if maximum trials reached
    if ($_SESSION['admin_password_confirmation_trial'] > 2) {
        // Clear session variables and redirect
        unsetVendorSessionVariables();
        echo '<script>';
        echo 'alert("Reached Maximum Trials");';
        echo 'window.location.href = "admin_index.php";';
        echo '</script>';
        exit(); // Ensure that the script stops execution after the header redirection
    }

    // Display incorrect credentials message
    echo '<script>';
    echo 'alert("Wrong Credentials!");';
    echo 'window.location.href = "admin_create_vendor_account_1.php";';
    echo '</script>';
}

// Function to unset vendor-related session variables
function unsetVendorSessionVariables()
{
    unset($_SESSION['vendor_first_name']);
    unset($_SESSION['vendor_last_name']);
    unset($_SESSION['vendor_full_name']);
    unset($_SESSION['vendor_stall_number']);
    unset($_SESSION['vendor_mobile_number']);
    unset($_SESSION['vendor_email']);
    unset($_SESSION['vendor_product_type']);
    //  unset($_SESSION['vendor_payment_basis']);
    unset($_SESSION['vendor_first_payment_date']);
    unset($_SESSION['vendor_userid']);
    unset($_SESSION['vendor_hashed_password']);
    unset($_SESSION['vendor_transaction_id']);
    unset($_SESSION['admin_password_confirmation_trial']);
}
