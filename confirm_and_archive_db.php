<?php
require_once('config.php');

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {


    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $vendorUserId = $_POST['vendorUserId'];
        $transactionId = $_POST['transactionId'];
        $balance = $_POST['balance']; // Retrieve the balance from the AJAX request

        // Fetch the vendor details from the database
        $getVendorQuery = "SELECT vendor_name, balance, mop FROM `ven_payments` WHERE `transaction_id` = ?";
        $getVendorStatement = mysqli_prepare($connect, $getVendorQuery);

        if ($getVendorStatement) {
            mysqli_stmt_bind_param($getVendorStatement, "s", $transactionId); // Use "s" for VARCHAR
            mysqli_stmt_execute($getVendorStatement);
            mysqli_stmt_bind_result($getVendorStatement, $vendorName, $vendorBalance, $modeOfPayment);
            mysqli_stmt_fetch($getVendorStatement);
            mysqli_stmt_close($getVendorStatement);

            // Assuming $paymentDate is received from your form or any other source
            $paymentDate = $_POST['paymentDate']; // Change this line based on your form data

            // Assuming $modeOfPayment is received from your form or any other source
            $modeOfPayment = $_POST['modeOfPayment'];

            $receiptNumber = generateReceiptNumber();
            $vendor_receipt_number = $receiptNumber;

            // Insert into the paid records table
            $insertPaidQuery = "INSERT INTO `paid_records` (vendor_userid, vendor_name, receipt_number, balance, payment_date, mop, transaction_id, admin_userid) VALUES (?, ?,?, ?, ?, ?, ?, ?)";
            $insertPaidStatement = mysqli_prepare($connect, $insertPaidQuery);

            if ($insertPaidStatement) {
                mysqli_stmt_bind_param($insertPaidStatement, "sssdssss", $vendorUserId, $vendorName, $vendor_receipt_number, $vendorBalance, $paymentDate, $modeOfPayment, $transactionId, $admin_userid); // Use the new transaction ID
                $successPaid = mysqli_stmt_execute($insertPaidStatement);

                if (!$successPaid) {
                    echo "Error inserting into paid records table: " . mysqli_error($connect);
                }
            } else {
                echo "Error preparing statement for paid records: " . mysqli_error($connect);
            }

            // Insert into the archive records table
            $insertArchiveQuery = "INSERT INTO `archive_records` (vendor_userid, vendor_name, balance, payment_date, mop, transaction_id) VALUES (?, ?, ?, ?, ?, ?)";
            $insertArchiveStatement = mysqli_prepare($connect, $insertArchiveQuery);

            if ($insertArchiveStatement) {
                mysqli_stmt_bind_param($insertArchiveStatement, "ssdsss", $vendorUserId, $vendorName, $vendorBalance, $paymentDate, $modeOfPayment, $transactionId); // Use the new transaction ID
                $successArchive = mysqli_stmt_execute($insertArchiveStatement);

                if (!$successArchive) {
                    echo "Error inserting into archive records table: " . mysqli_error($connect);
                }
            } else {
                echo "Error preparing statement for archive records: " . mysqli_error($connect);
            }

            // Update the ven_payments table for confirmation and archiving
            $updateQuery = "UPDATE `ven_payments` SET `confirmed` = 1, `archived` = 1 WHERE `vendor_userid` = ?";
            $updateStatement = mysqli_prepare($connect, $updateQuery);

            if ($updateStatement) {
                mysqli_stmt_bind_param($updateStatement, "s", $vendorUserId); // Use "s" for VARCHAR
                $successUpdate = mysqli_stmt_execute($updateStatement);

                if (!$successUpdate) {
                    echo "Error updating ven_payments table: " . mysqli_error($connect);
                } else {
                   // Update the balance in the vendor_user table
                    $newBalance = 0 - $balance; // Subtract $balance from the existing balance

                    // Generate a new transaction ID
                    $newTransactionId = generateUniqueTransactionId($connect, $vendorUserId);

                    // Update the transaction_id and balance in the vendor_balance table
                    $updateVendorBalanceQuery = "UPDATE `vendor_balance` SET `transaction_id` = ?, `balance` = `balance` + ? WHERE `vendor_userid` = ?";
                    $updateVendorBalanceStatement = mysqli_prepare($connect, $updateVendorBalanceQuery);

                    if ($updateVendorBalanceStatement) {
                        mysqli_stmt_bind_param($updateVendorBalanceStatement, "sds", $newTransactionId, $newBalance, $vendorUserId);
                        $successUpdateVendorBalance = mysqli_stmt_execute($updateVendorBalanceStatement);

                        if (!$successUpdateVendorBalance) {
                            echo "Error updating transaction_id and balance in vendor_balance table: " . mysqli_error($connect);
                        }
                    } else {
                        echo "Error preparing statement for updating transaction_id and balance in vendor_balance: " . mysqli_error($connect);
                    }

                    // Update the admin_stall_map table
                    $updateStallMapQuery = "UPDATE `admin_stall_map` SET `balance` = `balance` + ? WHERE `vendor_userid` = ?";
                    $updateStallMapStatement = mysqli_prepare($connect, $updateStallMapQuery);

                    if ($updateStallMapStatement) {
                        mysqli_stmt_bind_param($updateStallMapStatement, "ds", $newBalance, $vendorUserId);
                        $successUpdateStallMap = mysqli_stmt_execute($updateStallMapStatement);

                        if (!$successUpdateStallMap) {
                            echo "Error updating balance in admin_stall_map table: " . mysqli_error($connect);
                        }
                    } else {
                        echo "Error preparing statement for updating balance in admin_stall_map: " . mysqli_error($connect);
                    }

                    // Check for success in all updates
                    if ($successPaid && $successArchive && $successUpdate && $successUpdateVendorBalance && $successUpdateStallMap) {
                        echo "Payment confirmed and archived for Vendor: $vendorName";
                    } else {
                        echo "Error in one or more update operations.";
                    }
                }
            } else {
                echo "Error preparing statement for updating ven_payments: " . mysqli_error($connect);
            }
        } else {
            echo "Error preparing statement to fetch vendor details: " . mysqli_error($connect);
        }
    } else {
        echo "Invalid request method";
    }
} else {
    header("location:admin_logout.php");
}

// Function to generate a unique transaction ID
function generateUniqueTransactionId($connect, $vendorUserId)
{
    // Set the maximum number of attempts to generate a unique ID
    $maxAttempts = 10;

    for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        // Generate a secure random 6-digit number
        $uniqueId = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Concatenate vendor user ID and unique ID
        $transactionId = $vendorUserId . '-' . $uniqueId;

        // Check if the generated transaction_id already exists in vendor_balance table
        $checkIfExistsVendor = "SELECT transaction_id FROM vendor_balance WHERE transaction_id = '$transactionId'";
        $resultVendor = $connect->query($checkIfExistsVendor);

        // Check if the generated transaction_id already exists in paid_records table
        $checkIfExistsPaidRecords = "SELECT transaction_id FROM paid_records WHERE transaction_id = '$transactionId'";
        $resultPaidRecords = $connect->query($checkIfExistsPaidRecords);

        // If not exists in both tables, break the loop
        if ($resultVendor->num_rows === 0 && $resultPaidRecords->num_rows === 0) {
            return $transactionId;
        }
    }


    // If maximum attempts are reached, handle the error (e.g., throw an exception)
    throw new Exception("Failed to generate a unique transaction ID after $maxAttempts attempts");
}

function generateReceiptNumber()
{
    // Generate a unique identifier (you can use a function like uniqid())
    $uniqueId = uniqid();

    // Extract only the numerical part of the unique identifier
    $numericPart = hexdec(substr($uniqueId, 6, 8));

    // Get the current date and time
    $currentDateTime = new DateTime();
    $formattedDateTime = $currentDateTime->format('YmdHis');

    // Combine the numeric part and date/time
    $combinedString = $formattedDateTime . '_' . $numericPart;

    // Take the last 5 characters to limit the length to 5 digits
    $receiptNumber = substr($combinedString, -5);

    return $receiptNumber;
}
