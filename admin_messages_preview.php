<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Include database connection or functions
    // Example: include('db_connection.php');

    // Fetch vendor messages with the latest message for each vendor
    $query = "SELECT vendor_name, vendor_stall_number, MAX(latest_timestamp) as latest_timestamp FROM (
                    SELECT vendor_name, vendor_stall_number, vendor_timestamp as latest_timestamp
                    FROM vendor_messages
                    UNION
                    SELECT vendor_name, vendor_stall_number, admin_timestamp as latest_timestamp
                    FROM admin_messages
                 ) as combined_messages
                 GROUP BY vendor_name, vendor_stall_number
                 ORDER BY latest_timestamp DESC";

    // Execute the query and handle errors
    $result = $connect->query($query);
    if (!$result) {
        die("Error executing the query: " . $connect->error);
    }

    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Messages Preview</title>
    </head>

    <body>
        <h1>Messages Preview</h1>

        <?php
        // Loop through each vendor to display the preview
        while ($row = $result->fetch_assoc()) {
            $vendor_name = $row['vendor_name'];
            $vendor_stall_number = $row['vendor_stall_number'];
            $latest_timestamp = $row['latest_timestamp'];

            // Fetch the latest message for each vendor (consider both vendor_chat and admin_reply)
            $latest_message_query = "SELECT * FROM (
                                        SELECT vendor_name, vendor_stall_number, vendor_chat as message, vendor_timestamp as timestamp, NULL as admin_name
                                        FROM vendor_messages
                                        WHERE vendor_name = '$vendor_name' AND vendor_stall_number = '$vendor_stall_number'
                                        UNION
                                        SELECT vendor_name, vendor_stall_number, admin_reply as message, admin_timestamp as timestamp, admin_name
                                        FROM admin_messages
                                        WHERE vendor_name = '$vendor_name' AND vendor_stall_number = '$vendor_stall_number'
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
                echo "<a href='admin_messages.php?vendor_name=$recipient&vendor_stall_number=$stall_number'>View All Messages</a>";
            }
        }
        ?>

        <!-- Button to create a new message -->
        <a href='admin_create_message.php'><button>Create New Message</button></a>

        <!-- Back button -->
        <a href='admin_index.php'><button>Back</button></a>
    </body>

    </html>

    <?php
} else {
    header("location:admin_login.php");
}
?>
