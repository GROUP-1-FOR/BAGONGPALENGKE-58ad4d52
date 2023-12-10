<?php
// vendor_invoice_summary.php

// Include the configuration file
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    // Check if the necessary POST data is set, including the transaction_id
    if (
        isset($_POST['vendorName']) &&
        isset($_POST['vendorUserId']) &&
        isset($_POST['vendorStallNumber']) &&
        isset($_POST['balance']) &&
        isset($_POST['transactionId'])
    ) {
        // Get data from POST variables, including transaction_id
        $vendorName = $_POST['vendorName'];
        $vendorUserId = $_POST['vendorUserId'];
        $vendorStallNumber = $_POST['vendorStallNumber'];
        $balance = $_POST['balance'];
        $transactionId = $_POST['transactionId'];
    } else {
        // Redirect to the main page if data is not set
        header("Location: vendor_index.php");
        exit();
    }

    // Check if the "Cash" button is clicked
    if (isset($_POST['pay_cash'])) {
        // Insert payment data into ven_payments table
        $paymentDate = date('Y-m-d H:i:s');
        $paymentMethod = "CASH"; // Set the payment method to "CASH"

        $sqlInsertPayment = "INSERT INTO ven_payments (vendor_userid, vendor_name, balance, archived, confirmed, payment_date, mop, transaction_id) VALUES (?, ?, ?, 0, 0, ?, ?, ?)";
        $stmtInsertPayment = $connect->prepare($sqlInsertPayment);
        $stmtInsertPayment->bind_param('ssdsss', $vendorUserId, $vendorName, $balance, $paymentDate, $paymentMethod, $transactionId); // Use 's' for VARCHAR, 'd' for DOUBLE
        $stmtInsertPayment->execute();
        // Set payment status to "Payment request sent" in session
        // $_SESSION['payment_status'] = "Payment request sent. Please wait for confirmation.";

        // Redirect to the same page to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


    // Check if the "GCash" button is clicked
    if (isset($_POST['pay_gcash'])) {
        // Insert GCash payment data into ven_payments table (modify as needed)
        // ...
        // Redirect to the same page or another page based on your logic
        // ...
    }
?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Invoice Summary</title>
        <style>
            body {
                text-align: center;
                margin: 50px;
                background-color: #f2f2f2;
            }

            #invoice-summary {
                width: 50%;
                margin: auto;
                border: 3px solid #ccc;
                padding: 20px;
                background-color: #fff;
            }

            #invoice-summary h2 {
                color: #850F16;
            }

            #invoice-summary p {
                font-size: 1.2em;
                margin: 10px 0;
            }

            #payment-buttons {
                margin-top: 20px;
            }

            #payment-buttons button {
                margin: 10px;
                padding: 10px;
                background-color: #850F16;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <div id="invoice-summary">
            <h2>Invoice Summary</h2>
            <p><strong>Vendor Name:</strong> <?php echo $vendorName; ?></p>
            <p><strong>Vendor ID:</strong> <?php echo $vendorUserId; ?></p>
            <p><strong>Stall Number:</strong> <?php echo $vendorStallNumber; ?></p>
            <p><strong>Balance:</strong> $<?php echo number_format($balance, 2); ?></p>
            <!-- Add the Transaction ID -->
            <p><strong>Transaction ID:</strong> <?php echo $transactionId; ?></p>

            <p><strong>Payment Status:</strong> To be paid</p>
            <!-- ... -->

            <div id="payment-buttons">
                <!-- Cash Payment Button -->
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                    <input type="hidden" name="vendorUserId" value="<?php echo $vendorUserId; ?>">
                    <input type="hidden" name="vendorStallNumber" value="<?php echo $vendorStallNumber; ?>">
                    <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                    <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>">
                    <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">Pay with Cash</button>
                </form>

                <!-- GCash Payment Button -->
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                    <input type="hidden" name="vendorUserId" value="<?php echo $vendorUserId; ?>">
                    <input type="hidden" name="vendorStallNumber" value="<?php echo $vendorStallNumber; ?>">
                    <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                    <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>">
                    <button type="submit" name="pay_gcash" onclick="return confirm('Are you sure you want to pay with GCash?')">Pay with GCash</button>
                </form>
            </div>

            <!-- Add any additional information or details about the invoice -->
            <!-- ... -->

            <p><a href="vendor_index.php">Back</a></p>
        </div>
    </body>

    </html>
<?php
} else {
    header("location:vendor_logout.php");
}
?>