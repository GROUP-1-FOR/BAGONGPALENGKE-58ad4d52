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

        <div class="flex-row-body">
            <h2 class="message-header">MESSAGES</h2>
            <div class="hr" ></div>

            <div class="message-container">

                <div class="flex-box1">
                    <div class="main-container-message">

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

                                // Limit the message preview to 500 characters
                                $message_preview = substr($message_preview, 0, 300);
                                // Add "..." if the message exceeds 500 characters
                                if (strlen($latest_message_row['message']) > 300) {
                                    $message_preview .= '...';
                                }

                                // Display the preview

                                echo '<div class="admin-message-preview">';
                                echo "<a href='admin_messages.php?vendor_userid=$vendor_userid&vendor_name=$recipient&vendor_stall_number=$stall_number'>";
                                echo "<div class='messages'>";
                                echo '<div class="flex-box-message">';
                                echo '<img class="profile-icon" src="assets\images\sign-in\profile-pic.svg">';

                                echo '<div class="flex-box-column">';
                                echo "<h2 class='name-vendor'> $recipient</h2>";

                                echo "<div class='flex-box-row'>";
                                echo "<p class='subtitle-text'>Stall:</p>";
                                echo "<p class='sub-text'>$stall_number</p>";
                                echo "</div>";

                                if (!empty($admin_name)) {
                                    // If the latest message is an admin reply, display admin information
                                    echo "<div class='flex-box-row'>";
                                    echo "<p class='subtitle-text'>Replied by:</p>";
                                    echo "<p class='sub-text'> $admin_name</p>";
                                    echo "</div>";
                                }

                                echo "<div class='flex-box-row-message'>";
                                echo "<p class='subtitle-text'>Message:</p>";
                                echo "<p class='sub-text'> $message_preview </p>";
                                echo "</div>";

                                echo "</div>";



                                echo "</div>";
                                echo '</div>';
                                echo "</a>";
                                echo "<div class='hr'></div>";
                                echo '</div>';





                                // Create a clickable link to view all messages

                            }
                        }
                        ?>

                        <div>


                        </div>

                    </div>

                </div>

                <footer></footer>

            </div>

            <div class="button-placement">
                <center><a href='admin_createnew_message.php'><button class="create-message">Create New Message</button></a></center>
                <center><a href='admin_index.php'><button class="back-button6">
                            < Back</button></a></center>
            </div>

    </body>

    </html>
<?php
} else {
    // Redirect to messages preview if vendor_name or vendor_stall_number is not set
    header("location:admin_messages_preview.php");
}
