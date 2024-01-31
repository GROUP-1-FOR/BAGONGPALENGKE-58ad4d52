<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}
//to know last log in time of vendor
include('vendor_login_time.php');


// Fetch user data using prepared statement
$sqlUserData = "SELECT * FROM vendor_balance WHERE vendor_userid = ?";
$stmtUserData = $connect->prepare($sqlUserData);
$stmtUserData->bind_param('s', $userid); // Use 's' for VARCHAR
$stmtUserData->execute();
$resultUserData = $stmtUserData->get_result();

if ($resultUserData->num_rows > 0) {
    $rowUserData = $resultUserData->fetch_assoc();
    $vendorName = $rowUserData['vendor_name'];
    $stallNumber = $rowUserData['vendor_stall_number'];
    $balance = $rowUserData['balance'];
    $transactionId = $rowUserData['transaction_id'];
} else {
    // Handle the case where the user ID is not found or there's an issue with the database query
    header("location:vendor_login.php");
}

// Get the current date
$currentDate = new DateTime();
$currentDay = intval($currentDate->format('d'));
$currentMonth = intval($currentDate->format('m'));
$currentYear = intval($currentDate->format('Y'));

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

$startingDate = new DateTime($rowUserData['starting_date']);
if ($currentDate >= $startingDate) {
    if ($currentMonth > $rowUserData['month'] || $currentYear > $rowUserData['year']) {
        // Perform actions when the current date is greater than or equal to the starting date

        // Fetch vendor_product and vendor_payment_basis
        $vendorProduct = $rowUserData['vendor_product'];
        $vendorPaymentBasis = $rowUserData['vendor_payment_basis'];

        // Fetch stall_rate from rent_basis table based on vendor_product
        $sqlStallRate = "SELECT stall_rate FROM rent_basis WHERE vendor_product = ?";
        $stmtStallRate = $connect->prepare($sqlStallRate);
        $stmtStallRate->bind_param('s', $vendorProduct);
        $stmtStallRate->execute();
        $resultStallRate = $stmtStallRate->get_result();

        if ($resultStallRate->num_rows > 0) {
            $rowStallRate = $resultStallRate->fetch_assoc();
            $stallRate = $rowStallRate['stall_rate'];
        }
        if ($vendorPaymentBasis == 'Monthly') {
            // Calculate balance based on Monthly payment basis


            if ($currentYear == $rowUserData['year'] && $currentMonth > $rowUserData['month']) {
                $balance = ($currentMonth - $rowUserData['month']) * $stallRate;
            } elseif ($currentYear > $rowUserData['year']) {
                $newcurrentMonth = ($currentYear - $rowUserData['year']) * 12 + $currentMonth;
                $balance = ($newcurrentMonth - $rowUserData['month']) * $stallRate;
            }
            // Update current balance and remaining balance
            $currentBalance = $balance + $rowUserData['balance'];

            $sqlUpdateBalance = "UPDATE vendor_balance SET balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('di', $currentBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

            $sqlUpdateBalance = "UPDATE admin_stall_map SET balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('di', $currentBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

            // Update day, month, and year
            $sqlUpdateDate = "UPDATE vendor_balance SET day = ?, month = ?, year = ? WHERE vendor_userid = ?";
            $stmtUpdateDate = $connect->prepare($sqlUpdateDate);
            $stmtUpdateDate->bind_param('iiii', $currentDay, $currentMonth, $currentYear, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateDate->execute();
        }
    }
}
/*     // Debugging output
    echo "Current Day: $currentDay<br>";
    echo "Stored Day: {$rowUserData['day']}<br>";
    echo "Current Month: $currentMonth<br>";
    echo "Stored Month: {$rowUserData['month']}<br>";
    echo "Current Year: $currentYear<br>";
    echo "Stored Year: {$rowUserData['year']}<br>";
    echo "Vendor Product: $vendorProduct<br>";
    echo "Vendor Payment Basis: $vendorPaymentBasis<br>";
    echo "Stall Rate: $stallRate<br>";
     }*/





// Check the payment status
$paymentStatus = "To be paid";

// Check if the payment has been sent but not confirmed
$sqlCheckPayment = "SELECT * FROM ven_payments WHERE vendor_userid = ? AND vendor_name = ? AND transaction_id = ? AND confirmed = 0 AND archived = 0";
$stmtCheckPayment = $connect->prepare($sqlCheckPayment);
$stmtCheckPayment->bind_param('iss', $userid, $vendorName, $transactionId);
$stmtCheckPayment->execute();
$resultCheckPayment = $stmtCheckPayment->get_result();

if ($resultCheckPayment->num_rows > 0) {
    $paymentStatus = "Payment has already been sent";
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 70%;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Add this style for the "Pay" button inside the modal */
        #payButton {
            margin-top: 10px;
            padding: 15px;
            background-color: gray;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
    <script>
        function openModal() {
            document.getElementById('myModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        function pay() {
            // Now, submit the form
            document.getElementById('paymentForm').submit();
            closeModal();
        }
    </script>
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
                    <?php if ($balance > 0) : ?>
                        $<?php echo number_format($balance, 2); ?>
                        <?php if ($paymentStatus === "To be paid") : ?>
                            <!-- The "Pay" button triggers the modal directly -->
                            <br>
                            <button type="button" name="pay" onclick="openModal()">Pay</button>
                            <!-- The form to be submitted -->
                            <form id="paymentForm" method="post" action="vendor_invoice_summary.php">
                                <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                                <input type="hidden" name="vendorUserId" value="<?php echo $userid; ?>">
                                <input type="hidden" name="vendorStallNumber" value="<?php echo $stallNumber; ?>">
                                <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                                <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>"> <!-- Add this line -->
                            </form>
                        <?php endif; ?>
                    <?php else : ?>
                        $<?php echo number_format($balance, 2); ?>
                    <?php endif; ?>
                    <?php if ($paymentStatus === "Payment has already been sent") : ?>
                        <p>Payment has already been sent. Wait for confirmation.</p>
                    <?php endif; ?>
                </center>
            </td>
        </tr>
    </table>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p>Are you sure you want to make the payment?</p>
            <!-- The "Pay" button inside the modal -->
            <button id="payButton" type="button" onclick="pay()">Pay</button>
        </div>
    </div>
    <br>
    <a href=vendor_edit_profile.php>
        <h1>EDIT PROFILE</h1>
    </a>

    <a href=vendor_transaction_history.php>
        <h1>TRANSACTIONS</h1>
    </a>

    <a href=vendor_view_announcement.php>
        <h1>SEE ANNOUNCEMENTS</h1>
    </a>
    <a href="vendor_messages.php">
        <h1>MESSAGES</h1>
    </a>

    <a href="vendor_notification.php">
        <h1>NOTIFICATIONS</h1>
    </a>

    <a href=vendor_faq.php>
        <h1>HELP</h1>
    </a>

    <a href="vendor_send_report.php">
        <h1>REPORT</h1>
    </a>

    <a href=vendor_logout.php>
        <h1>LOGOUT</h1>
    </a>
</body>

</html>