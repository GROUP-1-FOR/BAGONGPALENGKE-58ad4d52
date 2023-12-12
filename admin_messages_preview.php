<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Include database connection or functions
    // Example: include('db_connection.php');


    // Fetch vendor messages with the latest message for each vendor
    $query = "SELECT vendor_userid, vendor_name, vendor_stall_number, MAX(latest_timestamp) as latest_timestamp FROM (
                    SELECT vendor_userid, vendor_name, vendor_stall_number, vendor_timestamp as latest_timestamp
                    FROM vendor_messages
                    UNION
                    SELECT vendor_userid, vendor_name, vendor_stall_number, admin_timestamp as latest_timestamp
                    FROM admin_messages
                 ) as combined_messages
                 GROUP BY vendor_userid, vendor_name, vendor_stall_number
                 ORDER BY latest_timestamp DESC";


        
    $sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";

    // Execute the query
    $result = $connect->query($sql);
    $admin_name = "";
    $admin_name_error = "";

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            $admin_name = $row['admin_name'];
        }
    } else {
        $admin_name_error = "No results found for user ID $admin_userId";
    }

    // Execute the query and handle errors
    $result = $connect->query($query);
    if (!$result) {
        die("Error executing the query: " . $connect->error);
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SIGN IN </title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body style="color: black;">
    <header>
        <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src">
    </header>
    <div class="main-sidebar">
        <ul class="sidebar-outside">
            <div class="profile-container">
                <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
                <img class="profile-design" src="assets\images\sign-in\profile-design.png">
                <p class="vendor-name">Welcome, <?php echo $admin_name; ?>! </p>
            </div>
        </ul>
        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">
                <li><a class="home-index" href=admin_index.php> Home </a></li>
                <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
                <li><a class="report-management" href="admin_send_report.php"> Report Management </a></li>
                <li><a class="help-button" href="admin_faq.php"> Help </a></li>
            </ul>
        </div>
        <div>
            <a href=admin_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>
    </div>
    <div class="flex-box">
    <main class="main-container">
<div class="dashboard-announcement">
    <?php
    // Loop through each vendor to display the preview
    while ($row = $result->fetch_assoc()) {
        $vendor_userid = $row['vendor_userid'];
        $vendor_name = $row['vendor_name'];
        $vendor_stall_number = $row['vendor_stall_number'];
        $latest_timestamp = $row['latest_timestamp'];

        // Fetch the latest message for each vendor (consider both vendor_chat and admin_reply)
        $latest_message_query = "SELECT * FROM (
                                    SELECT vendor_userid, vendor_name, vendor_stall_number, vendor_chat as message, vendor_timestamp as timestamp, NULL as admin_name
                                    FROM vendor_messages
                                    WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$vendor_name' AND vendor_stall_number = '$vendor_stall_number'
                                    UNION
                                    SELECT vendor_userid, vendor_name, vendor_stall_number, admin_reply as message, admin_timestamp as timestamp, admin_name
                                    FROM admin_messages
                                    WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$vendor_name' AND vendor_stall_number = '$vendor_stall_number'
                                 ) as combined_messages
                                 ORDER BY timestamp DESC
                                 LIMIT 1";

        // Execute the query and handle errors
        $latest_message_result = $connect->query($latest_message_query);
        if (!$latest_message_result) {
            die("Error executing the query: " . $connect->error);
        }

        if ($latest_message_row = $latest_message_result->fetch_assoc()) {
            $recipient = $latest_message_row['vendor_name'];
            $stall_number = $latest_message_row['vendor_stall_number'];
            $message_preview = $latest_message_row['message'];
            $admin_name = $latest_message_row['admin_name'];

            // Display the preview
            echo "<h3>Recipient: $recipient</h3>";
            echo "<p>Stall: $stall_number</p>";

            if (!empty($admin_name)) {
                // If the latest message is an admin reply, display admin information
                echo "<p>Replied by: $admin_name</p>";
            }

            echo "<p>Message: $message_preview</p>";

            // Create a clickable link to view all messages
            echo "<a href='admin_messages.php?vendor_userid=$vendor_userid&vendor_name=$recipient&vendor_stall_number=$stall_number'>View All Messages</a>";
        }
    }
    ?>

    <!-- Button to create a new message -->
    <a href='admin_createnew_message.php'><button>Create New Message</button></a>

    <!-- Back button -->
    <a href='admin_index.php'><button>Back</button></a>
    </main>
    <main>
<div>
</body>

</html>

<?php
} else {
    header("location:admin_logout.php");
}
?>
