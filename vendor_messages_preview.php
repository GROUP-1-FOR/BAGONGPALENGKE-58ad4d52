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
            WHERE receiver = ? OR sender = ?
            GROUP BY sender
            ORDER BY max_timestamp DESC
        ";
        $stmtLatestMessages = $connect->prepare($sqlLatestMessages);
        $stmtLatestMessages->bind_param('ss', $sender, $sender);
        $stmtLatestMessages->execute();
        $resultLatestMessages = $stmtLatestMessages->get_result();

        while ($rowLatestMessage = $resultLatestMessages->fetch_assoc()) {
            // Fetch the last message (sent or received) for each sender
            $latestSender = $rowLatestMessage['sender'];
            $sqlLastMessage = "
                SELECT *
                FROM messages
                WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
                ORDER BY timestamp DESC
                LIMIT 1
            ";
            $stmtLastMessage = $connect->prepare($sqlLastMessage);
            $stmtLastMessage->bind_param('ssss', $latestSender, $sender, $sender, $latestSender);
            $stmtLastMessage->execute();
            $resultLastMessage = $stmtLastMessage->get_result();

            if ($resultLastMessage->num_rows > 0) {
                $rowLastMessage = $resultLastMessage->fetch_assoc();
                $messageClass = ($rowLastMessage['sender'] == $sender) ? 'sent-preview' : 'received-preview';
                echo "<div class='message-preview " . $messageClass . "'>";
                echo "<p><a href='vendor_messages.php?sender=" . $latestSender . "'>";
                echo "Sender: " . $latestSender . "<br>";
                echo "Message: " . $rowLastMessage['message'] . "<br>";
                echo "Timestamp: " . $rowLastMessage['timestamp'] . "</a></p>";
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