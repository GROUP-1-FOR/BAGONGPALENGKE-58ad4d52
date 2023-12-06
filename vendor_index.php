<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
    //to know last log in time of vendor
    include('vendor_login_time.php');   
    // Fetch user data using prepared statement
    $sqlUserData = "SELECT * FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtUserData = $connect->prepare($sqlUserData);
    $stmtUserData->bind_param('s', $userid); // Use 's' for VARCHAR
    $stmtUserData->execute();
    $resultUserData = $stmtUserData->get_result();

    if ($resultUserData->num_rows > 0) {
        $rowUserData = $resultUserData->fetch_assoc();
        $vendorName = $rowUserData['vendor_name'];
        $stallNumber = $rowUserData['vendor_stall_number'];
        $balance = $rowUserData['balance'];
    } else {
        // Handle the case where the user ID is not found or there's an issue with the database query
        die("User not found or database query issue.");
    }

    // Check the payment status
    $paymentStatus = "To be paid";

    // Check if the payment has been sent but not confirmed
    $sqlCheckPayment = "SELECT * FROM ven_payments WHERE id = ? AND name = ? AND confirmed = 0 AND archived = 0";
    $stmtCheckPayment = $connect->prepare($sqlCheckPayment);
    $stmtCheckPayment->bind_param('is', $userid, $vendorName);
    $stmtCheckPayment->execute();
    $resultCheckPayment = $stmtCheckPayment->get_result();

    if ($resultCheckPayment->num_rows > 0) {
        $paymentStatus = "Payment has already been sent";
    }

    // Check if the payment is confirmed
    $sqlCheckPaymentConfirmation = "SELECT * FROM ven_payments WHERE id = ? AND name = ? AND confirmed = 1 AND archived = 1";
    $stmtCheckPaymentConfirmation = $connect->prepare($sqlCheckPaymentConfirmation);
    $stmtCheckPaymentConfirmation->bind_param('is', $userid, $vendorName);
    $stmtCheckPaymentConfirmation->execute();
    $resultCheckPaymentConfirmation = $stmtCheckPaymentConfirmation->get_result();

    if ($resultCheckPaymentConfirmation->num_rows > 0) {
        $paymentStatus = "The payment is confirmed";
        // Set balance to 0 as payment is confirmed
        $balance = $rowUserData['balance'];
    }

    // Check for payment status in session and reset the session variable
    if (isset($_SESSION['payment_status'])) {
        echo $_SESSION['payment_status'];
        unset($_SESSION['payment_status']);
    }
    // Check if the conditions allow redirecting to the invoice summary
    if (isset($_POST['pay']) && $paymentStatus === "To be paid" && $balance > 0) {
        // Check if there is an unconfirmed payment before redirecting
        if ($resultCheckPayment->num_rows === 0) {
            header("Location: vendor_invoice_summary.php?vendorName=$vendorName&vendorUserId=$userid&vendorStallNumber=$stallNumber&balance=$balance");
            exit();
        } else {
            echo "Cannot redirect to invoice summary because payment has already been sent but not confirmed.";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Main Page</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        #money-table {
            width: 70%;
            margin: auto;
            border-collapse: collapse;
            cursor: pointer;
        }

        #money-cell {
            border: 3px solid #ccc;
            padding: 50px;
            background-color: #850F16;
            color: white;
            font-size: 2em;
            /* Adjust the font size as needed */
        }

        #money-cell button {
            margin-top: 10px;
            padding: 40;
            background-color: gray;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1><?php echo "Hi, " . $vendorName; ?>!</h1>
    <!-- Display vendor information -->
    <p>Stall No: <?php echo $stallNumber; ?></p>
    <p>Vendor ID: <?php echo $userid; ?></p>

    <!-- Vendor Pay Table -->
    <table id="money-table">
    <tr>
        <td id="money-cell">
            <center>
                <?php if ($balance > 0): ?>
                    $<?php echo number_format($balance, 2); ?>
                    <?php if ($paymentStatus === "To be paid"): ?>
                        <form method="post" action="vendor_invoice_summary.php">
                            <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                            <input type="hidden" name="vendorUserId" value="<?php echo $userid; ?>">
                            <input type="hidden" name="vendorStallNumber" value="<?php echo $stallNumber; ?>">
                            <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                            <button type="submit" name="pay" onclick="return confirm('Are you sure you want to make the payment?')">Pay</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    $<?php echo number_format($balance, 2); ?>
                <?php endif; ?>
                <?php if ($paymentStatus === "Payment has already been sent"): ?>
                    <p>Payment has already been sent. Wait for confirmation.</p>
                <?php elseif ($paymentStatus === "The payment is confirmed"): ?>
                    <p>The payment is confirmed. You have no current balance.</p>
                <?php endif; ?>
            </center>
        </td>
    </tr>
</table>
    <br>
    <a href=vendor_edit_profile.php>
        <h1>EDIT PROFILE</h1>
    </a>
    <a href=vendor_view_announcement.php>
        <h1>SEE ANNOUNCEMENTS</h1>
    </a>
    <a href="vendor_messages.php">
        <h1>MESSAGES</h1>
    </a>

    <a href=vendor_logout.php>
        <h1>LOGOUT</h1>
    </a>
</body>

</html>
<?php
} else {
    header("location:vendor_login.php");
}
?>
