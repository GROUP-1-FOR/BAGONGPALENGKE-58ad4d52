<?php
require("config.php");

// Check if user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

include('admin_login_time.php');

$sqlName = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";
$resultName = $connect->query($sqlName);
$admin_name = "";
$admin_name_error = "";

// Check if any rows were returned
if ($resultName->num_rows > 0) {
    // Output data for each row
    while ($row = $resultName->fetch_assoc()) {
        $admin_name = $row['admin_name'];
    }
} else {
    $admin_name_error = "No results found for user ID $admin_userid";
}

// Get current date and time
$currentDateTime = date('F d, Y | h:i A');

// Fetch data from the latest row of admin_notification table
$sqlNotification = "SELECT notif_date, title, vendor_name FROM admin_notification ORDER BY notif_date DESC LIMIT 1";
$resultNotification = $connect->query($sqlNotification);
$latestNotificationDate = "";
$latestNotificationTitle = "";
$latestNotificationVendorName = "";

if ($resultNotification->num_rows > 0) {
    while ($rowNotification = $resultNotification->fetch_assoc()) {
        $latestNotificationDate = $rowNotification['notif_date'];
        $latestNotificationTitle = $rowNotification['title'];
        $latestNotificationVendorName = $rowNotification['vendor_name'];
    }
}

$sqlAllVendors = "SELECT * FROM vendor_balance";
$resultAllVendors = $connect->query($sqlAllVendors);

if ($resultAllVendors->num_rows > 0) {
    while ($rowUserData = $resultAllVendors->fetch_assoc()) {
        // Fetch vendor details for the current vendor
        $userid = $rowUserData['vendor_userid'];
        $vendorName = $rowUserData['vendor_name'];
        $stallNumber = $rowUserData['vendor_stall_number'];
        $balance = $rowUserData['balance'];
        $transactionId = $rowUserData['transaction_id'];

        // Get the current date
        $currentDate = new DateTime();
        $currentDay = 10;//intval($currentDate->format('d'));
        $currentMonth = 3;//intval($currentDate->format('m'));
        $currentYear = 2024;//intval($currentDate->format('Y'));

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
                    $stmtUpdateBalance->bind_param('ds', $currentBalance, $userid); // Assuming vendor_userid is of type integer
                    $stmtUpdateBalance->execute();

                    $sqlUpdateBalance = "UPDATE admin_stall_map SET balance = ?, due = 1, paid = 0 WHERE vendor_userid = ?";
                    $stmtUpdateBalance = $connect->prepare($sqlUpdateBalance);
                    $stmtUpdateBalance->bind_param('ds', $currentBalance, $userid); // Assuming vendor_userid is of type integer
                    $stmtUpdateBalance->execute();

                    // Update day, month, and year
                    $sqlUpdateDate = "UPDATE vendor_balance SET day = ?, month = ?, year = ? WHERE vendor_userid = ?";
                    $stmtUpdateDate = $connect->prepare($sqlUpdateDate);
                    $stmtUpdateDate->bind_param('iiis', $currentDay, $currentMonth, $currentYear, $userid); // Assuming vendor_userid is of type integer
                    $stmtUpdateDate->execute();
                }
            }
        }
    }
} else {
    // Handle the case where there are no vendors in the vendor_balance table
    echo "No vendors found in the database.";
}

// Fetch data from admin_stall_map table
$sqlStallMap = "SELECT paid, due, vacant FROM admin_stall_map";
$resultStallMap = $connect->query($sqlStallMap);

// Initialize variables for totals
$totalPaid = 0;
$totalOngoing = 0;
$totalVacant = 74; // Initial value

// Check if any rows were returned
if ($resultStallMap->num_rows > 0) {
    while ($rowStallMap = $resultStallMap->fetch_assoc()) {
        $totalPaid += $rowStallMap['paid'];
        $totalOngoing += $rowStallMap['due'];
        $totalVacant -= $rowStallMap['vacant'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Homepage</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<header class="header2"></header>

<?php include 'sidebar.php'; ?>
<div class="head">

    <img class="public-market-pic-v2" src="assets\images\sign-in\public-market-head-v2.svg" alt="back-layer">

    <div class="head-bottom">
        <div>
            <p class="user-name"> <Strong>Welcome, </Strong> <?php echo $admin_name  ?>! </p> <br />
            <img class="head-bottom-1" src="assets\images\sign-in\name-holder2.svg" alt="back-layer">
        </div>
        <div>
            <p class="admin-datetime-text"> Date and Time:</p>
            <p class="admin-datetime"><?php echo $currentDateTime; ?></p>
            <img class="head-bottom-2" src="assets\images\sign-in\datetime-holder3.svg" alt="back-layer">
        </div>
    </div>

    <div class="head-bottom3">
        <div class="flex-column2">
            <div class="dashboard-announcement3">
                <div class="flex-row-1">
                    <div>

                        <h2 class="interactive-map-header">Interactive map</h2>
                        <div class="status-heading">
                            <div class=interactive-map-position>
                                <h1 class="rent-status-header"> Rent <br>Status </h1>
                            </div>
                            <div>
                                <p class="index-notifs"> Paid: <?php echo $totalPaid; ?> </p>
                                <button class="index-notifs"> Ongoing: <?php echo $totalOngoing; ?> </button>
                                <button class="index-notifs"> Vacant: <?php echo max(0, $totalVacant); ?> </button>
                            </div>
                        </div>
                    </div>
                    <div class="map-placement">
                        <a href=admin-map.php><img class=" map-icon" src="assets\images\sign-in\map-icon.svg" alt="map"> </a>
                    </div>
                </div>

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
                <center><a href=admin_notification.php><input class="submit-button3" type="submit" value="View"></a></center>
            </div>
        </div>

        <div class="dashboard-map">
            <center>
                <a href=admin_payment_records.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\payment-record.svg" alt="Sticker" class="sticker"> PAID RECORDS </button> </a>
                <a href=admin_send_announcement.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\announce.svg" alt="Sticker" class="sticker">ANNOUNCEMENTS </button> </a>
                <a href=admin_confirmpay.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\pay.svg" alt="Sticker" class="sticker"> PAYMENTS </button> </a>
                <a href=admin_messages.php><button class="index-buttons"> <img class="icons" src="assets\images\sign-in\messages.svg" alt="Sticker" class="sticker"> INBOX </button> </a>
            </center>
        </div>
    </div>
</div>
<footer></footer>

</html>