<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
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
        die("User not found or database query issue.");
    }

        // Get the current date
        //$currentDate = new DateTime();
        $currentDate = new DateTime('2023-12-15');
        $currentDay = 15;//intval($currentDate->format('d')); 
        $currentMonth = 12; //intval($currentDate->format('m'));
        $currentYear = 2023;//intval($currentDate->format('Y'));

// Compare with starting date from $rowUserData
//$startingDate = new DateTime($rowUserData['starting_date']);
$startingDate = new DateTime('2023-11-29');
if ($currentDate >= $startingDate) {
    // Perform actions when the current date is greater than or equal to the starting date

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

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

    // Check $vendorPaymentBasis
    if ($vendorPaymentBasis == 'Daily') {
        // Calculate balance based on Daily payment basis
        if ($currentYear == $rowUserData['year'] && $currentMonth == $rowUserData['month'] && $currentDay > $rowUserData['day']) {
            $balance = ($currentDay - $rowUserData['day']) * ($stallRate / $daysInMonth);

             // Update current balance and remaining balance
             $currentBalance = $balance + $rowUserData['balance'];
             $currentremainingBalance = $rowUserData['remaining_balance'] -$balance;
         
             $sqlUpdateBalance = "UPDATE vendor_balance SET balance = ?, remaining_balance = ? WHERE vendor_userid = ?";
             $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
             $stmtUpdateBalance->bind_param('ddi', $currentBalance, $currentremainingBalance, $userid); // Assuming vendor_userid is of type integer
             $stmtUpdateBalance->execute();

        } elseif ($currentYear == $rowUserData['year'] && $currentMonth > $rowUserData['month']) {

            $balance = ($currentDay * ($stallRate / $daysInMonth)) + $rowUserData['remaining_balance'];

            $newstallRate = ($currentMonth - $rowUserData['month']) * $stallRate;
            $newremainingBalance = $rowUserData['remaining_balance'] + $newstallRate;

            // Update remaining_balance
            $sqlUpdateBalance = "UPDATE vendor_balance SET remaining_balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('di', $newremainingBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

            // Update current balance and remaining balance
            $currentBalance = $balance + $rowUserData['balance'];
            $currentremainingBalance = $rowUserData['remaining_balance'] -$balance;
        
            $sqlUpdateBalance = "UPDATE vendor_balance SET balance = ?, remaining_balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('ddi', $currentBalance, $currentremainingBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

            
        } elseif ($currentYear > $rowUserData['year']) {

            $balance = ($currentDay * ($stallRate / $daysInMonth)) + $rowUserData['remaining_balance'];

            $newcurrentMonth = ($currentYear - $rowUserData['year']) * 12 + $currentMonth;
            $newstallRate = ($newcurrentMonth - $rowUserData['month']) * $stallRate;
            $newremainingBalance = $rowUserData['remaining_balance'] + $newstallRate;

            // Update remaining_balance
            $sqlUpdateBalance = "UPDATE vendor_balance SET remaining_balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('di', $newremainingBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

            // Update current balance and remaining balance
            $currentBalance = $balance + $rowUserData['balance'];
            $currentremainingBalance = $rowUserData['remaining_balance'] -$balance;
        
            $sqlUpdateBalance = "UPDATE vendor_balance SET balance = ?, remaining_balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('ddi', $currentBalance, $currentremainingBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();

           

        }
    } elseif ($vendorPaymentBasis == 'Monthly') {
        // Calculate balance based on Monthly payment basis
        if ($currentYear == $rowUserData['year'] && $currentMonth > $rowUserData['month']) {
            $balance = ($currentMonth - $rowUserData['month']) * $stallRate;
        } elseif ($currentYear > $rowUserData['year']) {
            $newcurrentMonth = ($currentYear - $rowUserData['year']) * 12 + $currentMonth;
            $balance = ($newcurrentMonth - $rowUserData['month']) * $stallRate;
        }
         // Update current balance and remaining balance
            $currentBalance = $balance + $rowUserData['balance'];
            $currentremainingBalance = $rowUserData['remaining_balance'] -$balance;
        
            $sqlUpdateBalance = "UPDATE vendor_balance SET balance = ?, remaining_balance = ? WHERE vendor_userid = ?";
            $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
            $stmtUpdateBalance->bind_param('ddi', $currentBalance, $currentremainingBalance, $userid); // Assuming vendor_userid is of type integer
            $stmtUpdateBalance->execute();
    }

    

      // Update day, month, and year
        $sqlUpdateDate = "UPDATE vendor_balance SET day = ?, month = ?, year = ? WHERE vendor_userid = ?";
        $stmtUpdateDate = $connect->prepare($sqlUpdateDate);
        $stmtUpdateDate->bind_param('iiii', $currentDay, $currentMonth, $currentYear, $userid); // Assuming vendor_userid is of type integer
        $stmtUpdateDate->execute();

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
    $sqlCheckPayment = "SELECT * FROM ven_payments WHERE id = ? AND name = ? AND transaction_id = ? AND confirmed = 0 AND archived = 0";
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
                        <?php if ($balance > 0) : ?>
                            $<?php echo number_format($balance, 2); ?>
                            <?php if ($paymentStatus === "To be paid") : ?>
                                <form method="post" action="vendor_invoice_summary.php">
                                    <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                                    <input type="hidden" name="vendorUserId" value="<?php echo $userid; ?>">
                                    <input type="hidden" name="vendorStallNumber" value="<?php echo $stallNumber; ?>">
                                    <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                                    <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>"> <!-- Add this line -->
                                    <button type="submit" name="pay" onclick="return confirm('Are you sure you want to make the payment?')">Pay</button>
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
    header("location:vendor_logout.php");
}
?>