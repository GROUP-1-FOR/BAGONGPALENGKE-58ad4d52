<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

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
            <title>Messages for <?php echo $recipient; ?></title>
            <style>
                body {
                    background-color: white;
                    color: maroon;
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }

                h1 {
                    color: maroon;
                }

                #message-container {
                    max-height: 300px;
                    /* Adjust the max-height as needed */
                    overflow-y: auto;
                    background-color: white;
                    border: 1px solid maroon;
                    padding: 10px;
                }

                #message-container p {
                    margin: 0;
                }

                form {
                    margin-top: 10px;
                }

                button {
                    background-color: maroon;
                    color: white;
                    padding: 5px 10px;
                    border: none;
                    cursor: pointer;
                }
            </style>
        </head>

        <body class="dashboard-messages">
            <center>
                <h1>Messages for <?php echo $recipient; ?></h1>

                <!-- Display messages -->
                <div id="message-container">
                    <?php
                    // Display messages
                    while ($message_row = $messages_result->fetch_assoc()) {
                        $message_type = ucfirst($message_row['message_type']);
                        $message_text = $message_row['message'];
                        $message_timestamp = $message_row['timestamp'];

                        echo "<p>$message_type: $message_text</p>";
                        echo "<p>Timestamp: $message_timestamp</p>";
                        echo "-----------------------";
                    }
                    ?>
                </div>

                <!-- Reply Form -->
                <form class="dashboard-messages"action="process_admin_reply.php" method="post">
                    <input type="hidden" name="admin_name" value="<?php echo $_SESSION["admin_name"]; ?>">
                    <input type="hidden" name="recipient" value="<?php echo $recipient; ?>">
                    <input type="hidden" name="stall_number" value="<?php echo $stall_number; ?>">
                    <label for="admin_reply">Admin Reply:</label>
                    <textarea name="admin_reply" id="admin_reply" required></textarea>
                    <br>
                    <button type="submit">Reply</button>
                </form>
                <br>
                <!-- Back button -->
                <a href='admin_messages_preview.php'><button>Back</button></a>
        </body>
        </center>

        </html>

<?php
    } else {
        // Redirect to messages preview if vendor_name or vendor_stall_number is not set
        header("location:admin_messages_preview.php");
    }
} else {
    header("location:admin_logout.php");
}
?>