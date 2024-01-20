<?php
// gcash_pay.php

// Include the configuration file
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Retrieve data from vendor_balance table
    $sqlGetBalance = "SELECT vendor_name, balance, transaction_id FROM vendor_balance WHERE vendor_userid = ?";
    $stmtGetBalance = $connect->prepare($sqlGetBalance);
    $stmtGetBalance->bind_param('s', $userid);
    $stmtGetBalance->execute();
    $stmtGetBalance->bind_result($vendorName, $balance, $transactionId);
    $stmtGetBalance->fetch();
    $stmtGetBalance->close();

    if (isset($_POST['pay_button'])) {
        // Process payment
        $paymentDate = date('Y-m-d H:i:s');
        $paymentMethod = "GCASH";

        // Insert payment details into ven_payments table
        $sqlInsertPayment = "INSERT INTO ven_payments (vendor_userid, vendor_name, balance, archived, confirmed, payment_date, mop, transaction_id) VALUES (?, ?, ?, 0, 0, ?, ?, ?)";
        $stmtInsertPayment = $connect->prepare($sqlInsertPayment);
        $stmtInsertPayment->bind_param('ssdsss', $userid, $vendorName, $balance, $paymentDate, $paymentMethod, $transactionId);
        $stmtInsertPayment->execute();
        $stmtInsertPayment->close();

        // Insert notification into admin_notification table
        $notifTitle = "Payment Confirmation Request";
        $confirmValue = 1;

        $sqlInsertNotification = "INSERT INTO admin_notification (vendor_userid, vendor_name, transaction_id, title, confirm, mop, notif_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
        $stmtInsertNotification->bind_param('ssssiss', $userid, $vendorName, $transactionId, $notifTitle, $confirmValue, $paymentMethod, $paymentDate);
        $stmtInsertNotification->execute();
        $stmtInsertNotification->close();

        // Redirect or perform additional actions after successful payment
        header("Location: vendor_index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>GCash Payment Confirmation</title>
    <!-- Add your styles here -->
</head>

<body>
    <div id="payment-confirmation">
            <h2>YOU ARE ABOUT TO PAY</h2>
            <p>Amount</p>
            <p>PHP <?php echo number_format($balance, 2); ?></p>
            <p>Total</p>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return confirmPayment()">
                <button type="submit" name="pay_button">PAY PHP <?php echo number_format($balance, 2); ?></button>
                <input type="hidden" name="payment_confirmed" value="1">
                <button type="button" onclick="window.location.href='vendor_invoice_summary.php'">Cancel</button>
            </form>
        </div>

        <script>
            function confirmPayment() {
                // Display a confirmation dialog
                var confirmation = confirm("Are you sure you want to proceed with the payment?");
                
                // If user clicks OK, set the hidden input value to 1
                if (confirmation) {
                    document.getElementsByName("payment_confirmed")[0].value = "1";
                }

                // If user clicks OK, the form will be submitted; otherwise, it will be canceled
                return confirmation;
            }
        </script>
</body>

</html>

<?php
} else {
    header("location: vendor_logout.php");
}
?>
