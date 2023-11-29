<?php
// create_new_message.php

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

    // Fetch all admin names for the dropdown menu
    $sqlAllAdmins = "SELECT admin_name FROM admin_sign_in";
    $resultAllAdmins = $connect->query($sqlAllAdmins);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create New Message - Vendor</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        h1 {
            color: #333;
        }

        .new-message-form {
            width: 60%;
            margin: auto;
            padding: 20px;
            border: 3px solid #ccc;
            background-color: #fff;
            margin-top: 20px;
        }

        .new-message-form label {
            display: block;
            margin-bottom: 10px;
        }

        .new-message-form select,
        .new-message-form textarea {
            width: 100%;
            margin-bottom: 10px;
        }

        .new-message-form button {
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
    <h1>Create New Message - Vendor</h1>

    <div class="new-message-form">
        <form method="post" action="">
            <label for="receiver">Select Receiver:</label>
            <select name="receiver" required>
                <?php
                while ($row = $resultAllAdmins->fetch_assoc()) {
                    echo "<option value='" . $row['admin_name'] . "'>" . $row['admin_name'] . "</option>";
                }
                ?>
            </select>
            <br>
            <label for="message">Message:</label>
            <textarea name="message" required></textarea>
            <br>
            <button type="submit" name="submit_message">Send Message</button>
        </form>
    </div>

    <a href="vendor_messages_preview.php">Back to Messages Preview</a>

</body>

</html>

<?php
    if (isset($_POST['submit_message'])) {
        // Handle form submission for new message
        $receiver = $_POST['receiver'];
        $message = $_POST['message'];

        // Insert the new message into the messages table
        $sqlInsertMessage = "INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('sss', $sender, $receiver, $message);
        $stmtInsertMessage->execute();

        // Redirect to the messages preview page after sending the new message
        header("Location: vendor_messages_preview.php");
        exit();
    }
} else {
    header("location:vendor_login.php");
}
?>