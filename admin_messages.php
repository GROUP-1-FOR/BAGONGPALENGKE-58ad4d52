<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

// Fetch admin_name from the database
$admin_info_query = "SELECT admin_name FROM admin_sign_in WHERE admin_id = '$admin_id' LIMIT 1";
$admin_info_result = $connect->query($admin_info_query);

if ($admin_info_result->num_rows > 0) {
    $admin_info = $admin_info_result->fetch_assoc();
    $admin_name = $admin_info['admin_name'];

    // Store admin_name in the session
    $_SESSION["admin_name"] = $admin_name;
}

// Check if the vendor_name and vendor_stall_number are set in the URL
if (isset($_GET['vendor_name']) && isset($_GET['vendor_stall_number'])) {
    $recipient = $_GET['vendor_name'];
    $stall_number = $_GET['vendor_stall_number'];

    // Fetch the vendor_userid based on vendor_name and vendor_stall_number
    $vendor_userid_query = "SELECT vendor_userid FROM vendor_sign_in WHERE vendor_name = '$recipient' AND vendor_stall_number = '$stall_number' LIMIT 1";
    $vendor_userid_result = $connect->query($vendor_userid_query);

    if ($vendor_userid_result->num_rows > 0) {
        $vendor_userid_row = $vendor_userid_result->fetch_assoc();
        $vendor_userid = $vendor_userid_row['vendor_userid'];
    } else {
        // Handle the case where vendor_userid is not found
        die("Error: Vendor userid not found.");
    }

    // Fetch messages for the selected vendor, both vendor and admin messages
    $messages_query = "
        SELECT 'vendor' as message_type, vendor_chat as message, vendor_timestamp as timestamp
        FROM vendor_messages
        WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$recipient' AND vendor_stall_number = '$stall_number'
        
        UNION ALL
        
        SELECT 'admin' as message_type, admin_reply as message, admin_timestamp as timestamp
        FROM admin_messages
        WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$recipient' AND vendor_stall_number = '$stall_number'
        
        ORDER BY timestamp ASC";

    // Execute the query and handle errors
    $messages_result = $connect->query($messages_query);
    if (!$messages_result) {
        die("Error executing the query: " . $connect->error);
    }

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
    </head>

    <body>
        <header></header>
        <?php include 'sidebar.php'; ?>

        <div class="flex-row">
            <h2 class="message-container-header">MESSAGES</h2>
            <tr>
                <div class="message-container">

                    <div class="flex-box1">
                        <div class="main-container">



                        </div>

                    </div>
                </div>

        </div>


        <footer></footer>
    </body>

    </html>
<?php
} else {
    // Redirect to messages preview if vendor_name or vendor_stall_number is not set
    header("location:admin_messages_preview.php");
}
