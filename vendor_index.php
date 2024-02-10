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

$currentDateTime = date('F d, Y | h:i A');
// Fetch data from the latest row of vendor_notification table
$sqlNotification = "SELECT notif_date, title, admin_name FROM vendor_notification ORDER BY notif_date DESC LIMIT 1";
$resultNotification = $connect->query($sqlNotification);
$latestNotificationDate = "";
$latestNotificationTitle = "";
$latestNotificationVendorName = "";

if ($resultNotification->num_rows > 0) {
    while ($rowNotification = $resultNotification->fetch_assoc()) {
        $latestNotificationDate = $rowNotification['notif_date'];
        $latestNotificationTitle = $rowNotification['title'];
        $latestNotificationVendorName = $rowNotification['admin_name'];
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
            font-size: small;
            height: 200px;
        }

        #money-table {
            position: relative;
            z-index: 5;
            width: 300px;
            margin: auto;
            border-collapse: collapse;
            cursor: pointer;
            height: 100px;
            font-size: small;
            
        }

        #money-cell {
            margin-left: 50px;
            background-color: transparent;
            color: white;
            font-size: 40px;
            z-index: 10;

        }

        .error-message2 {
            font-size: x-small;
            color: green !important;
            margin-left: -20px;

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
            z-index: 10;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 70%;
            text-align: center;
            z-index: 999;
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
            margin-top: 5px;
            padding: 15px;
            background-color: gray;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            z-index: 10;


            background-color: #850f16;
            font-weight: bolder;
            color: white !important;
            border-radius: 4px;
            height: 35px;
            border-style: solid;
            border-color: transparent;
            white-space: nowrap;
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
<header class="header2"></header>
<?php include 'sidebar2.php'; ?>

<div class="head">
    <img class="public-market-pic-v2" src="assets\images\sign-in\public-market-head-v2.svg" alt="back-layer">

    <div class="head-bottom">
        <div>
            <p class="user-name">Welcome, <?php echo $vendorName; ?>! </p> <br />
            <img class="head-bottom-1" src="assets\images\sign-in\name-holder2.svg" alt="back-layer">
        </div>

        <div>
            <p class="admin-datetime-text"> Date and Time</p>
            <p class="admin-datetime"><?php echo $currentDateTime; ?></p>
            <img class="head-bottom-2" src="assets\images\sign-in\datetime-holder3.svg" alt="back-layer">
        </div>

    </div>

    <div class="head-bottom3">
        <div class="flex-column2">
            <div class="dashboard-announcement6">
                <div class="flex-row-1">
                    <div>
                        <h2 class="interactive-map-header3">AMOUNT TO PAY</h2>
                        <div class="status-heading">
                            <div class=interactive-map-position>
                                <table id="money-table">
                                    <tr>
                                        <td id="money-cell">
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
                                                <p class="error-message2">Payment has already been sent. Wait for confirmation.</p>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                </table>
                                <div id="myModal" class="modal">
                                    <div class="modal-content">
                                        <span class="close error-message" onclick="closeModal()">&times;</span>
                                        <p>Are you sure you want to make the payment?</p>
                                        <!-- The "Pay" button inside the modal -->
                                        <button class="submit-button" id="payButton" type="button" onclick="pay()">Pay</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <a href=admin-map.php><img class="map-icon" src="assets\images\sign-in\map-icon.svg" alt="map"> Amount to Pay </a> -->

                </div>
                <img class="vendor-img" src="assets\images\sign-in\vendor-dashboard-img.svg" alt="back-layer">
            </div>
            <div class="dashboard-announcementv2">

                <div class="flex-row-1">
                    <h2 class="notification-header">Notifications</h2>
                    <div class="message-notif">
                    <p class="admin-datetime-text-v2"><?php echo $latestNotificationDate; ?></p>
                    <h1 class="admin-message-notif"><?php echo $latestNotificationTitle; ?></h1>
                    <p class="admin-vendor-notif">From: <?php echo $latestNotificationVendorName; ?></p>
                    </div>
                </div>
                <center><a href=vendor_notification.php><input class="submit-button3" type="submit" value="View"></a></center>
            </div>
        </div>

        <div class="dashboard-map">
            <center>
                <a href="vendor_view_announcement.php"><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\announce.svg" alt="Sticker" class="sticker"> ANNOUNCEMENTS </button> </a>
                <a href="vendor_transaction_history.php"><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\transactions.svg" alt="Sticker" class="sticker">TRANSACTIONS </button> </a>
                <a href="vendor_edit_profile.php"><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\edit-profile.svg" alt="Sticker" class="sticker"> EDIT PROFILE </button> </a>
                <a href="vendor_messages.php"><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\messages.svg" alt="Sticker" class="sticker"> MESSAGES </button> </a>
            </center>
        </div>



    </div>
</div>





<footer></footer>

</html>