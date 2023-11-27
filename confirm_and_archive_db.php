<?php
require_once('config.php');


if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $vendorId = $_POST['vendorId'];

        // Fetch the vendor details from the database
        $getVendorQuery = "SELECT name, balance FROM `ven_payments` WHERE `id` = ?";
        $getVendorStatement = mysqli_prepare($connect, $getVendorQuery);

        if ($getVendorStatement) {
            mysqli_stmt_bind_param($getVendorStatement, "s", $vendorId); // Use "s" for VARCHAR
            mysqli_stmt_execute($getVendorStatement);
            mysqli_stmt_bind_result($getVendorStatement, $vendorName, $vendorBalance);
            mysqli_stmt_fetch($getVendorStatement);
            mysqli_stmt_close($getVendorStatement);

            // Assuming $paymentDate is received from your form or any other source
            $paymentDate = $_POST['paymentDate']; // Change this line based on your form data

            // Insert into the paid records table
            $insertPaidQuery = "INSERT INTO `paid_records` (name, balance, payment_date) VALUES (?, ?, ?)";
            $insertPaidStatement = mysqli_prepare($connect, $insertPaidQuery);

            if ($insertPaidStatement) {
                mysqli_stmt_bind_param($insertPaidStatement, "sds", $vendorName, $vendorBalance, $paymentDate); // Use "s" for VARCHAR, "d" for DECIMAL, and "s" for DATE
                $successPaid = mysqli_stmt_execute($insertPaidStatement);

                if (!$successPaid) {
                    echo "Error inserting into paid records table: " . mysqli_error($connect);
                }
            } else {
                echo "Error preparing statement for paid records: " . mysqli_error($connect);
            }

            // Insert into the archive records table
            $insertArchiveQuery = "INSERT INTO `archive_records` (name, balance, payment_date) VALUES (?, ?, ?)";
            $insertArchiveStatement = mysqli_prepare($connect, $insertArchiveQuery);

            if ($insertArchiveStatement) {
                mysqli_stmt_bind_param($insertArchiveStatement, "sds", $vendorName, $vendorBalance, $paymentDate); // Use "s" for VARCHAR, "d" for DECIMAL, and "s" for DATE
                $successArchive = mysqli_stmt_execute($insertArchiveStatement);

                if (!$successArchive) {
                    echo "Error inserting into archive records table: " . mysqli_error($connect);
                }
            } else {
                echo "Error preparing statement for archive records: " . mysqli_error($connect);
            }
            // Update the ven_payments table for confirmation and archiving
            $updateQuery = "UPDATE `ven_payments` SET `confirmed` = 1, `archived` = 1 WHERE `id` = ?";
            $updateStatement = mysqli_prepare($connect, $updateQuery);

            if ($updateStatement) {
                mysqli_stmt_bind_param($updateStatement, "s", $vendorId); // Use "s" for VARCHAR
                $successUpdate = mysqli_stmt_execute($updateStatement);
    
                if (!$successUpdate) {
                    echo "Error updating ven_payments table: " . mysqli_error($connect);
                } else {
    // Update the balance in the vendor_user table
                    $newBalance = 0;
                    $updateBalanceQuery = "UPDATE `vendor_sign_in` SET `balance` = ? WHERE `vendor_userid` = ?";
                    $updateBalanceStatement = mysqli_prepare($connect, $updateBalanceQuery);
    
                    if ($updateBalanceStatement) {
                        mysqli_stmt_bind_param($updateBalanceStatement, "ds", $newBalance, $vendorId); // Use "d" for DECIMAL and "s" for VARCHAR
                        $successUpdateBalance = mysqli_stmt_execute($updateBalanceStatement);
    
                        if (!$successUpdateBalance) {
                            echo "Error updating balance in vendor_user table: " . mysqli_error($connect);
                        }
                    } else {
                        echo "Error preparing statement for updating balance: " . mysqli_error($connect);
                    }
                }
            } else {
                echo "Error preparing statement for updating ven_payments: " . mysqli_error($connect);
            }
    
            if ($successPaid && $successArchive && $successUpdate && $successUpdateBalance) {
                echo "Payment confirmed and archived for Vendor: $vendorName";
            }
        } else {
            echo "Error preparing statement to fetch vendor details: " . mysqli_error($connect);
        }
    } else {
        echo "Invalid request method";
    }
} else {
    header("location:admin_login.php");
}
