<?php
// admin_messages.php

require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_userid = $_SESSION["userid"];

    // Fetch admin data using prepared statement
    $sqlAdmin = "SELECT * FROM admin_sign_in WHERE admin_userid = ?";
    $stmtAdmin = $connect->prepare($sqlAdmin);
    $stmtAdmin->bind_param('s', $admin_userid); // Use 's' for VARCHAR
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows > 0) {
        $rowAdmin = $resultAdmin->fetch_assoc();
        $sender = $rowAdmin['admin_name'];
    } else {
        // Handle the case where the admin ID is not found or there's an issue with the database query
        die("Admin not found or database query issue.");
    }

    // Fetch the list of vendor receivers
    $sqlVendors = "SELECT vendor_name FROM vendor_sign_in";
    $resultVendors = $connect->query($sqlVendors);

    if (!$resultVendors) {
        die("Error fetching vendor list: " . $connect->error);
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

        // Redirect after form submission to avoid resubmission on page refresh
        header("Location: admin_messages.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Messages Page - Admin</title>
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
    <h1>Reply to Messages</h1>

    <form id="message-form" method="post" action="">
        <label for="receiver">Select Receiver:</label>
        <select name="receiver" required>
            <?php
            while ($rowVendor = $resultVendors->fetch_assoc()) {
                echo "<option value='" . $rowVendor["vendor_name"] . "'>" . $rowVendor["vendor_name"] . "</option>";
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
            $stmtReceivedMessages->bind_param('s', $sender);
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

        <button type="submit" name="send_message">Send Message</button>
    </form>

    <a href="admin_main_page.php">Back to Main Page</a>

</body>

</html>

<?php
} else {
    header("location:admin_login.php");
}
?>