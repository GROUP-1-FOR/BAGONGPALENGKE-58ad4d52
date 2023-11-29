<?php
// vendor_messages_preview.php

require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch vendor data using prepared statement
    $sqlVendor = "SELECT vendor_name FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtVendor = $connect->prepare($sqlVendor);
    $stmtVendor->bind_param('s', $userid); // Use 's' for VARCHAR
    $stmtVendor->execute();
    $resultVendor = $stmtVendor->get_result();

    if ($resultVendor->num_rows > 0) {
        $rowVendor = $resultVendor->fetch_assoc();
        $sender = $rowVendor['vendor_name'];
    } else {
        die("Error: Vendor name not found for user ID $userid");
    }

    // Fetch and display the latest message for each sender
    $sqlLatestMessages = "
        SELECT sender, MAX(timestamp) AS max_timestamp
        FROM messages
        WHERE receiver = ?
        GROUP BY sender
    ";
    $stmtLatestMessages = $connect->prepare($sqlLatestMessages);
    $stmtLatestMessages->bind_param('s', $sender);
    $stmtLatestMessages->execute();
    $resultLatestMessages = $stmtLatestMessages->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Messages Preview - Vendor</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        h1 {
            color: #333;
        }

        .message-preview {
            width: 60%;
            margin: auto;
            padding: 20px;
            border: 3px solid #ccc;
            background-color: #fff;
            margin-bottom: 20px;
        }

        .message-preview p {
            margin: 10px 0;
        }

        .sent-preview {
            background-color: #ccc;
            color: #333;
        }

        .received-preview {
            background-color: #ffcccc;
            color: #850F16;
        }

        a {
            text-decoration: none;
            color: #850F16;
        }

        a:hover {
            text-decoration: underline;
            color: #333;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>

<body>
    <h1>Messages Preview - Vendor</h1>

    <div>
    <?php
    // Fetch and display the latest message for each sender
    $sqlLatestMessages = "
        SELECT sender, MAX(timestamp) AS max_timestamp
        FROM messages
        WHERE receiver = ?
        GROUP BY sender
        ORDER BY max_timestamp DESC
    ";
    $stmtLatestMessages = $connect->prepare($sqlLatestMessages);
    $stmtLatestMessages->bind_param('s', $sender);
    $stmtLatestMessages->execute();
    $resultLatestMessages = $stmtLatestMessages->get_result();

    while ($rowLatestMessage = $resultLatestMessages->fetch_assoc()) {
        // Fetch all messages for each sender
        $latestSender = $rowLatestMessage['sender'];
        $sqlAllMessages = "
            SELECT *
            FROM messages
            WHERE sender = ? AND receiver = ?
            ORDER BY timestamp DESC
        ";
        $stmtAllMessages = $connect->prepare($sqlAllMessages);
        $stmtAllMessages->bind_param('ss', $latestSender, $sender);
        $stmtAllMessages->execute();
        $resultAllMessages = $stmtAllMessages->get_result();

        if ($resultAllMessages->num_rows > 0) {
            // Display the latest message in the preview with appropriate styling
            $rowLatestMessageDetails = $resultAllMessages->fetch_assoc();
            $messageClass = ($rowLatestMessageDetails['sender'] == $sender) ? 'sent-preview' : 'received-preview';
            echo "<div class='message-preview " . $messageClass . "'>";
            echo "<p><a href='admin_messages.php?sender=" . $latestSender . "'>";
            echo "Sender: " . $latestSender . "<br>";
            echo "Message: " . $rowLatestMessageDetails['message'] . "<br>";
            echo "Timestamp: " . $rowLatestMessageDetails['timestamp'] . "</a></p>";
            echo "</div>";
        }
    }
    ?>
</div>
    <div>
    <a href="create_new_message.php">Create New Message</a>
    </div>
    <a href="vendor_index.php">Back to Main Page</a>

</body>

</html>

<?php
} else {
    header("location:vendor_login.php");
}
?>