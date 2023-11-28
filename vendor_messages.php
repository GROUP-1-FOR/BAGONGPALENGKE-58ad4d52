<?php
// vendor_messages.php

require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

            // Fetch user data using prepared statement
    $sqlVendors = "SELECT * FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtVendors = $connect->prepare($sqlVendors);
    $stmtVendors->bind_param('s', $userid); // Use 's' for VARCHAR
    $stmtVendors->execute();
    $resultVendors = $stmtVendors->get_result();

    if ($resultVendors->num_rows > 0) {
        $row = $resultVendors->fetch_assoc();
        $sender = $row['vendor_name'];
    } else {
        // Handle the case where the user ID is not found or there's an issue with the database query
        die("User not found or database query issue.");
    }

    // Fetch the list of admin receivers
    $sqlAdmins = "SELECT admin_name FROM admin_sign_in";
    $resultAdmins = $connect->query($sqlAdmins);

    if (!$resultAdmins) {
        die("Error fetching admin list: " . $connect->error);
    }

    // Process the form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $receiver = $_POST["receiver"];
        $message = $_POST["message"];

        // Insert the message into the messages table
    $sqlInsertMessage = "INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
    $stmtInsertMessage->bind_param('sss', $sender, $receiver, $message);
    $stmtInsertMessage->execute();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Messages Page</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        #message-form {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 3px solid #ccc;
            background-color: #fff;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
        }

        button {
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
    <h1>Send a Message</h1>

    <form id="message-form" method="post" action="">
        <label for="receiver">Select Receiver:</label>
        <select name="receiver" required>
            <?php
            while ($rowAdmin = $resultAdmins->fetch_assoc()) {
                echo "<option value='" . $rowAdmin["admin_name"] . "'>" . $rowAdmin["admin_name"] . "</option>";
            }
            ?>
        </select>

        <div>
            <?php
            // Fetch and display sent messages
            $sqlSentMessages = "SELECT * FROM messages WHERE sender = ?";
            $stmtSentMessages = $connect->prepare($sqlSentMessages);
            $stmtSentMessages->bind_param('s', $sender);
            $stmtSentMessages->execute();
            $resultSentMessages = $stmtSentMessages->get_result();

            echo "<h2>Sent Messages</h2>";
            while ($rowSentMessage = $resultSentMessages->fetch_assoc()) {
                echo "<p>Receiver: " . $rowSentMessage['receiver'] . "<br>";
                echo "Message: " . $rowSentMessage['message'] . "<br>";
                echo "Timestamp: " . $rowSentMessage['timestamp'] . "</p>";
            }

            // Fetch and display received messages
            $sqlReceivedMessages = "SELECT * FROM messages WHERE receiver = ?";
            $stmtReceivedMessages = $connect->prepare($sqlReceivedMessages);
            $stmtReceivedMessages->bind_param('s', $sender);  // Use the sender (admin name) as the receiver
            $stmtReceivedMessages->execute();
            $resultReceivedMessages = $stmtReceivedMessages->get_result();

            echo "<h2>Received Messages</h2>";
            while ($rowReceivedMessage = $resultReceivedMessages->fetch_assoc()) {
                echo "<p>Sender: " . $rowReceivedMessage['sender'] . "<br>";
                echo "Message: " . $rowReceivedMessage['message'] . "<br>";
                echo "Timestamp: " . $rowReceivedMessage['timestamp'] . "</p>";
}
            ?>
        </div>

        <label for="message">Message:</label>
        <textarea name="message" rows="4" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <a href="vendor_main_page.php">Back to Main Page</a>

</body>

</html>

<?php
} else {
    header("location:vendor_login.php");
}
?>