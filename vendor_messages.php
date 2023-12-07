<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];
    $vendorName = ''; // Initialize vendor name

    // Fetch vendor data using prepared statement
    $sqlVendorData = "SELECT vendor_name, vendor_stall_number FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtVendorData = $connect->prepare($sqlVendorData);
    $stmtVendorData->bind_param('s', $userid);
    $stmtVendorData->execute();
    $resultVendorData = $stmtVendorData->get_result();

    if ($resultVendorData->num_rows > 0) {
        $rowVendorData = $resultVendorData->fetch_assoc();
        $vendorName = $rowVendorData['vendor_name'];
        $vendorStallNumber = $rowVendorData['vendor_stall_number'];
    } else {
        // Handle the case where the vendor data is not found or there's an issue with the database query
        die("Vendor data not found or database query issue.");
    }

    // Process the form submission if the user sends a message
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_message'])) {
        $messageText = $_POST['message_text'];

        // Insert the message into the vendor_message table with the current timestamp
        $sqlInsertMessage = "INSERT INTO vendor_messages (vendor_name, vendor_stall_number, vendor_chat, vendor_timestamp) VALUES (?, ?, ?, NOW())";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('sss', $vendorName, $vendorStallNumber, $messageText);
        $stmtInsertMessage->execute();

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
        <title>Vendor Messages</title>
        <style>
            body {
                color: maroon;
                font-family: Arial, sans-serif;
            }

            #message-container {
                max-height: 300px;
                overflow-y: auto;
                background-color: white;
                /* Set your desired background color */
                border: 1px solid maroon;
                padding: 10px;
                width: 60%;
                /* Set the width to 60% */
                margin: 0 auto;
                /* Center the container */
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

    <body>
        <center>
            <h1>Welcome, <?php echo $vendorName; ?>! </h1>

            <!-- Display messages -->
            <div id="message-container">
                <?php
                while ($rowMessage = $resultFetchAllMessages->fetch_assoc()) :
                ?>
                    <p><?php echo ucfirst($rowMessage['message_type']); ?>: <?php echo $rowMessage['message']; ?> (<?php echo $rowMessage['timestamp']; ?>)</p>
                <?php endwhile; ?>
            </div>

            <!-- Form to send a message -->
            <form method="post">
                <textarea name="message_text" rows="4" cols="50" placeholder="Type your message here"></textarea>
                <br>
                <button type="submit" name="send_message">Send Message</button>
            </form>

            <br>
            <a href="vendor_index.php">
                <h1>BACK</h1>
            </a>
        </center>
    </body>

    </html>

<?php
} else {
    header("location:vendor_logout.php");
}
?>