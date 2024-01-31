<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];
    $vendorName = ''; // Initialize vendor name
} else {
    header("location:vendor_logout.php");
}

// Fetch vendor data using prepared statement
$sqlVendorData = "SELECT vendor_name, vendor_stall_number, vendor_userid FROM vendor_sign_in WHERE vendor_userid = ?";
$stmtVendorData = $connect->prepare($sqlVendorData);
$stmtVendorData->bind_param('s', $userid);
$stmtVendorData->execute();
$resultVendorData = $stmtVendorData->get_result();

if ($resultVendorData->num_rows > 0) {
    $rowVendorData = $resultVendorData->fetch_assoc();
    $vendorName = $rowVendorData['vendor_name'];
    $vendorStallNumber = $rowVendorData['vendor_stall_number'];
    $vendorUserId = $rowVendorData['vendor_userid']; // New line to fetch vendor_userid
} else {
    // Handle the case where the vendor data is not found or there's an issue with the database query
    die("Vendor data not found or database query issue.");
}

// Process the form submission if the user sends a message
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_message'])) {
    $messageText = $_POST['message_text'];

    // Insert the message into the vendor_message table with the current timestamp
    $sqlInsertMessage = "INSERT INTO vendor_messages (vendor_name, vendor_stall_number, vendor_chat, vendor_timestamp, vendor_userid) VALUES (?, ?, ?, NOW(), ?)";
    $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
    $stmtInsertMessage->bind_param('ssss', $vendorName, $vendorStallNumber, $messageText, $vendorUserId);
    $stmtInsertMessage->execute();

    // Insert a notification into the admin_notification table
    $notifTitle = "You have a Message!";
    $messageValue = 1; // Set the confirm value to 1
    $timestamp = date('Y-m-d H:i:s'); // Get the current timestamp

    $sqlInsertNotification = "INSERT INTO admin_notification (vendor_userid, vendor_name, title, message, notif_date) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
    $stmtInsertNotification->bind_param('sssis', $vendorUserId, $vendorName, $notifTitle, $messageValue, $timestamp);
    $stmtInsertNotification->execute();

    // Redirect to the same page after processing the form
    header("Location: vendor_messages.php");
    exit();
}

// Fetch all messages (both vendor and admin) in ascending order of timestamp
$sqlFetchAllMessages = "
        SELECT 'vendor' as message_type, vendor_chat as message, vendor_timestamp as timestamp
        FROM vendor_messages
        WHERE vendor_name = ? AND vendor_stall_number = ?

        UNION ALL

        SELECT 'admin' as message_type, admin_reply as message, admin_timestamp as timestamp
        FROM admin_messages
        WHERE vendor_name = ? AND vendor_stall_number = ?

        ORDER BY timestamp ASC";

$stmtFetchAllMessages = $connect->prepare($sqlFetchAllMessages);
$stmtFetchAllMessages->bind_param('ssss', $vendorName, $vendorStallNumber, $vendorName, $vendorStallNumber);
$stmtFetchAllMessages->execute();
$resultFetchAllMessages = $stmtFetchAllMessages->get_result();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            color: white;
        }


        .messaging-container {
            display: flex;
            height: 100vh;
            width: 1250px;
        }

        p {
            font-family: Helvetica, sans-serif !important;
        }

        .admin-panel,
        .vendor-panel {
            flex: 1;
            max-width: 300px;
            padding: 20px;
            background-color: #f0f0f0;
        }

        .vendor-panel {
            border-left: 1px solid #ccc;
        }

        .conversation-panel {
            flex: 3;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .message-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .admin-panel .message-input {
            border-color: #ccc;
        }

        .vendor-panel .message-input {
            border-color: #ddd;
        }

        .message {
            margin-bottom: 10px;
            width: 1000px;
            padding: 10px;
            border-radius: 5px;
        }

        .admin-message {

            color: white;
            margin-top: 20px;
            width: 500px;
            align-self: flex-start;
            background-color: #7A7D7C;
            margin-bottom: 10px;
            width: 700px;
            padding: 10px;
            border-radius: 5px;
        }

        .timestamp {
            margin-left: 320px;
            z-index: 4;
            margin-top: 5px;
            font-size: large;
            color: white;
            font-style: italic;
            font-size: xx-small;
            font-weight: 700;

        }

        .vendor-message {
            margin-top: 20px;
            width: 500px;
            align-self: flex-end;
            background-color: #9C3D42;
            color: white;
            margin-bottom: 10px;
            width: 700px;
            padding: 10px;
            border-radius: 5px;


        }
    </style>


</head>

<body>
    <header></header>
    <?php include 'sidebar2.php'; ?>

    <div class="flex-row-body">
        <div class="message-back-header">
            <a class="back-button7" href='vendor_index.php'>
                <img class="back-icon" src="assets\images\sign-in\back-icon.svg"></a>
            <h2 class="message-header-v2">Messages</h2>
        </div>
        <div class="recepient-panel">

        </div>
        <div class="hr"></div>


        <div class="recipient-box">
            <label class="recipient">Welcome, <?php echo $vendorName; ?>! </label>
            <p class="message-datetime">December 25 | 10:30 PM</p>
        </div>

        <!-- <div class="convo-container"> -->
        <div class="text-messages">
            <div class="message-container2">
                <?php
                while ($rowMessage = $resultFetchAllMessages->fetch_assoc()) :
                    $messageType = ucfirst($rowMessage['message_type']);
                    $messageText = $rowMessage['message'];
                    $timestamp = $rowMessage['timestamp'];
                ?>

                    <p class="<?php echo strtolower($messageType); ?>-message">
                        <?php echo "{$messageType}: {$messageText} <span class='timestamp'>{$timestamp}</span>"; ?>

                    <?php endwhile; ?>
            </div>
        </div>
        <!-- </div> -->
        <div class="background">
            <form class="flex-box-row-send" method="post">
                <textarea class="text-area2" name="message_text" rows="4" cols="50" placeholder="Type your message here"></textarea>
                <button class="send-button2" type="submit" name="send_message">Send</button>
            </form>
        </div>

        <footer></footer>

    </div>


</body>

</html>