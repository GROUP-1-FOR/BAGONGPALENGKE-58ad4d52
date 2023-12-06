<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if (!isset($_SESSION['admin_password_confirmation_trial'])) {
        $_SESSION['admin_password_confirmation_trial'] = 0;
    }

    $admin_password_confirmation_trial = $_SESSION['admin_password_confirmation_trial'];

    $vendor_first_name = $_SESSION['vendor_first_name'];
    $vendor_last_name =  $_SESSION['vendor_last_name'];
    $vendor_full_name = $_SESSION['vendor_full_name'];
    $vendor_stall_number = $_SESSION['vendor_stall_number'];
    $vendor_mobile_number = $_SESSION['vendor_mobile_number'];
    $vendor_email = $_SESSION['vendor_email'];
    $vendor_product_type = $_SESSION['vendor_product_type'];
    $vendor_userid = $_SESSION['vendor_userid'];
    $vendor_password = $_SESSION['vendor_hashed_password'];
    $vendor_transaction_id = $_SESSION['vendor_transaction_id'];






    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $admin_password = htmlspecialchars($_POST["admin_password"]);
        $hashedAdminPassword = md5($admin_password);

        $result = mysqli_query($connect, "SELECT * FROM admin_sign_in WHERE admin_userid= '$admin_userid'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {

            if ($hashedAdminPassword == $row["admin_password"]) {

                $sql1 = "INSERT INTO vendor_sign_in (vendor_first_name, vendor_last_name, vendor_name, vendor_stall_number, vendor_mobile_number, vendor_product, vendor_email, vendor_userid, vendor_password) 
    VALUES ('$vendor_first_name', '$vendor_last_name', '$vendor_full_name', '$vendor_stall_number', '$vendor_mobile_number', '$vendor_product_type', '$vendor_email', '$vendor_userid', '$vendor_password')";

                // Second insertion into vendor_balance table
                $sql2 = "INSERT INTO vendor_balance (vendor_name, vendor_stall_number, vendor_userid, balance, transaction_id) 
    VALUES ('$vendor_full_name', '$vendor_stall_number', '$vendor_userid', '0.00', '$vendor_transaction_id')";
                if ($connect->query($sql1) === TRUE) {
                    if ($connect->query($sql2) === TRUE) {
                        // Both insertions successful
                        echo '<script>';
                        echo 'alert("Vendor Account Created Successfully!");';
                        echo 'window.location.href = "admin_index.php";';
                        echo '</script>';

                        unset($_SESSION['vendor_first_name']);
                        unset($_SESSION['vendor_last_name']);
                        unset($_SESSION['vendor_full_name']);
                        unset($_SESSION['vendor_stall_number']);
                        unset($_SESSION['vendor_mobile_number']);
                        unset($_SESSION['vendor_email']);
                        unset($_SESSION['vendor_product_type']);
                        unset($_SESSION['vendor_userid']);
                        unset($_SESSION['vendor_hashed_password']);
                        unset($_SESSION['vendor_transaction_id']);
                    } else {
                        // If the second insertion fails, display an error
                        echo "Error: " . $sql2 . "<br>" . $connect->error;
                    }
                } else {
                    // If the first insertion fails, display an error
                    echo "Error: " . $sql1 . "<br>" . $connect->error;
                }

                // Close the database connection
                $connect->close();
            } else {
                $admin_password_confirmation_trial++;
                $_SESSION['admin_password_confirmation_trial'] = $admin_password_confirmation_trial;

                if ($admin_password_confirmation_trial > 2) {
                    unset($_SESSION['vendor_first_name']);
                    unset($_SESSION['vendor_last_name']);
                    unset($_SESSION['vendor_full_name']);
                    unset($_SESSION['vendor_stall_number']);
                    unset($_SESSION['vendor_mobile_number']);
                    unset($_SESSION['vendor_email']);
                    unset($_SESSION['vendor_product_type']);
                    unset($_SESSION['vendor_userid']);
                    unset($_SESSION['vendor_hashed_password']);
                    unset($_SESSION['vendor_transaction_id']);
                    unset($_SESSION['admin_password_confirmation_trial']);

                    echo '<script>';
                    echo 'alert("Reached Maximum Trials");';
                    echo 'window.location.href = "admin_index.php";';
                    echo '</script>';
                    exit(); // Ensure that the script stops execution after the header redirection
                }
                echo '<script>';
                echo 'alert("Wrong Credentials!");';
                echo 'window.location.href = "admin_create_vendor_account_1.php";';
                echo '</script>';
            }
        } else {
            echo '<script>';
            echo 'alert("No admin Found!");';
            echo 'window.location.href = "admin_index.php";';
            echo '</script>';
        }
    }
}
