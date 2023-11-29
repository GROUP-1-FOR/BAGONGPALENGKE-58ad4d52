<?php
// admin_messages.php

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

    // Fetch the selected sender from the URL parameter
    if (isset($_GET['sender'])) {
        $selectedSender = $_GET['sender'];
    } else {
        die("Error: Sender not specified in the URL");
    }

    // Fetch and display all messages for the selected sender (sent and received)
    $sqlAllMessages = "
        SELECT *
        FROM messages
        WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
        ORDER BY timestamp ASC";
    $stmtAllMessages = $connect->prepare($sqlAllMessages);
    $stmtAllMessages->bind_param('ssss', $sender, $selectedSender, $selectedSender, $sender);
    $stmtAllMessages->execute();
    $resultAllMessages = $stmtAllMessages->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Messages - Admin</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        h1 {
            color: #333;
        }

        .message-container {
            width: 60%;
            margin: auto;
            margin-bottom: 20px;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            width: 80%;
            max-width: 80%;
            word-wrap: break-word;
        }

        .sent-message {
            background-color: #ccc;
            align-self: flex-end;
            color: #333;
        }

        .received-message {
            background-color: #ffcccc;
            align-self: flex-start;
            color: #850F16;
        }

        .message p {
            margin: 5px 0;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #333;
        }

        .reply-form {
            width: 60%;
            margin: auto;
            padding: 20px;
            border: 3px solid #ccc;
            background-color: #fff;
            margin-top: 20px;
        }

        .reply-form textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }

        .reply-form button {
            background-color: #850F16;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>All Messages - Admin</h1>

    <div class="message-container">
        <?php
        while ($rowMessage = $resultAllMessages->fetch_assoc()) {
            echo "<div class='message " . ($rowMessage['sender'] == $sender ? 'sent-message' : 'received-message') . "'>";
            echo "<p>";
            echo "Sender: " . $rowMessage['sender'] . "<br>";
            echo "Message: " . $rowMessage['message'] . "<br>";
            echo "Timestamp: " . $rowMessage['timestamp'] . "</p>";
            echo "</div>";
        }
        ?>
    </div>

    <div class="reply-form">
        <form method="post" action="">
            <label for="reply_message">Reply:</label>
            <textarea name="reply_message" required></textarea>
            <br>
            <button type="submit" name="submit_reply">Send Reply</button>
        </form>
    </div>

    <a href="admin_messages_preview.php">Back to Messages Preview</a>

</body>

</html>

<?php
    if (isset($_POST['submit_reply'])) {
        // Handle form submission for reply
        $replyMessage = $_POST['reply_message'];

        // Insert the reply into the messages table
        $sqlInsertReply = "INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
        $stmtInsertReply = $connect->prepare($sqlInsertReply);
        $stmtInsertReply->bind_param('sss', $sender, $selectedSender, $replyMessage);
        $stmtInsertReply->execute();

        // Refresh the page after replying to display the updated messages
        header("Location: admin_messages.php?sender=$selectedSender");
        exit();
    }
} else {
    header("location:admin_login.php");
}
?>