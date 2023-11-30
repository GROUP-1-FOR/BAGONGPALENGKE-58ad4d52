<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Include database connection or functions
    // Example: include('db_connection.php');

    // Fetch vendor messages
    $query = "SELECT DISTINCT vendor_name, vendor_stall_number FROM vendor_messages ORDER BY vendor_timestamp DESC";

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

            // Fetch the latest message for each vendor
            $latest_message_query = "SELECT * FROM vendor_messages WHERE vendor_name = '$vendor_name' AND vendor_stall_number = '$vendor_stall_number' ORDER BY vendor_timestamp DESC LIMIT 1";

            // Execute the query and handle errors
            $latest_message_result = $connect->query($latest_message_query);
            if (!$latest_message_result) {
                die("Error executing the query: " . $connect->error);
            }

            if ($latest_message_row = $latest_message_result->fetch_assoc()) {
                $recipient = $latest_message_row['vendor_name'];
                $stall_number = $latest_message_row['vendor_stall_number'];
                $message_preview = $latest_message_row['vendor_chat'];

                // Display the preview
                echo "<h3>Recipient: $recipient</h3>";
                echo "<p>Stall: $stall_number</p>";
                echo "<p>Message: $message_preview</p>";

                // Create a clickable link to view all messages
                echo "<a href='admin_messages.php?vendor_name=$recipient&vendor_stall_number=$stall_number'>View All Messages</a>";
            }
        }
        ?>

        <!-- Button to create a new message -->
        <a href='admin_create_message.php'><button>Create New Message</button></a>

        <!-- Back button -->
        <a href='admin_main_page.php'><button>Back</button></a>
    </body>

    </html>

    <?php
} else {
    header("location:admin_login.php");
}
?>
