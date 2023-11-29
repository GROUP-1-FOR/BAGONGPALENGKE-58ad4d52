<?php
// admin_messages_preview.php

require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_userid = $_SESSION["userid"];

    // Fetch admin data using prepared statement
    $sqlAdmin = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = ?";
    $stmtAdmin = $connect->prepare($sqlAdmin);
    $stmtAdmin->bind_param('s', $admin_userid); // Use 's' for VARCHAR
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows > 0) {
        $rowAdmin = $resultAdmin->fetch_assoc();
        $sender = $rowAdmin['admin_name'];
    } else {
        die("Error: Admin name not found for user ID $admin_userid");
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
    <title>Messages Preview - Admin</title>
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
    <h1>Messages Preview - Admin</h1>

    <div>
        <?php
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

    <a href="admin_index.php">Back to Main Page</a>

</body>

</html>

<?php
} else {
    header("location:admin_login.php");
}
?>