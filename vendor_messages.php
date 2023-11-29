<?php
// vendor_messages.php

// Include the config.php file and check for session variables
require("config.php");

$successMessage = $errorMessage = '';

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    // Your existing code for session variables goes here

    // Additional code for the vendor_messages page can be added below
    // For example, fetching and displaying messages from the database

    // Sample code to handle message submission (replace it with your form handling logic)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_message"])) {
        // Get the message from the form
        $vendor_messages = $_POST["vendor_messages"];

        // Retrieve vendor_name and vendor_stall_number from the vendor_sign_in table
        $vendor_userid = $_SESSION["userid"];

        $sqlFetchVendorInfo = "SELECT vendor_name, vendor_stall_number FROM vendor_sign_in WHERE vendor_userid = ?";
        $stmtFetchVendorInfo = $connect->prepare($sqlFetchVendorInfo);
        $stmtFetchVendorInfo->bind_param('s', $vendor_userid);
        $stmtFetchVendorInfo->execute();
        $resultFetchVendorInfo = $stmtFetchVendorInfo->get_result();

        if ($resultFetchVendorInfo->num_rows > 0) {
            $rowVendorInfo = $resultFetchVendorInfo->fetch_assoc();
            $vendor_name = $rowVendorInfo['vendor_name'];
            $vendor_stall_number = $rowVendorInfo['vendor_stall_number'];

            // Insert the message into the system_messages table
            $sqlInsertMessage = "INSERT INTO system_messages (vendor_messages, vendor_name, vendor_stall_number) VALUES (?, ?, ?)";
            $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
            $stmtInsertMessage->bind_param('sss', $vendor_messages, $vendor_name, $vendor_stall_number);
            $stmtInsertMessage->execute();

            // Display a success message or perform additional actions if needed
            $successMessage = "Message sent successfully!";
        } else {
            // Handle the case where vendor information is not found
            $errorMessage = "Vendor information not found.";
        }

        // Redirect to prevent form resubmission
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

// Fetch and display messages sent by the current vendor
if (isset($_SESSION["userid"])) {
    $vendor_userid = $_SESSION["userid"];

    $sqlFetchVendorInfo = "SELECT vendor_name, vendor_stall_number FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtFetchVendorInfo = $connect->prepare($sqlFetchVendorInfo);
    $stmtFetchVendorInfo->bind_param('s', $vendor_userid);
    $stmtFetchVendorInfo->execute();
    $resultFetchVendorInfo = $stmtFetchVendorInfo->get_result();

    if ($resultFetchVendorInfo->num_rows > 0) {
        $rowVendorInfo = $resultFetchVendorInfo->fetch_assoc();
        $vendor_name = $rowVendorInfo['vendor_name'];
        $vendor_stall_number = $rowVendorInfo['vendor_stall_number'];

        $sqlFetchMessages = "SELECT * FROM system_messages WHERE vendor_name = ? AND vendor_stall_number = ?";
        $stmtFetchMessages = $connect->prepare($sqlFetchMessages);
        $stmtFetchMessages->bind_param('ss', $vendor_name, $vendor_stall_number);
        $stmtFetchMessages->execute();
        $resultFetchMessages = $stmtFetchMessages->get_result();

       
    } else {
        // Handle the case where vendor information is not found
        echo "Vendor information not found.";
    }
} else {
    // Handle the case where the user is not logged in
    echo "User not logged in.";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 60%;
        }

        form {
            margin-top: 20px;
        }

        textarea {
            margin-bottom: 10px;
            width: 100%;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-top: 10px;
        }

        .error-message {
            background-color: #ff6666;
            color: white;
            padding: 10px;
            margin-top: 10px;
        }

        .additional-message {
            margin-top: 20px;
            background-color: #f2f2f2;
            padding: 10px;
        }

        /* Style for the messages */
        h2 {
            margin-top: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            border: 1px solid #ddd;
            margin: 5px;
            padding: 10px;
            background-color: #fff;
        }

        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    if ($successMessage) {
        echo "<div class='success-message'>$successMessage</div>";
    } elseif ($errorMessage) {
        echo "<div class='error-message'>$errorMessage</div>";
    }
    ?>

        <!-- Display messages sent by the current vendor -->
        <?php // Check if the query was successful
    if ($resultFetchMessages) {
        // Display messages sent by the current vendor
        if ($resultFetchMessages->num_rows > 0) {
            echo "<h2> Messages</h2>";
            echo "<ul>";
            while ($rowMessage = $resultFetchMessages->fetch_assoc()) {
                echo "<li>";
                echo "<p>Message: {$rowMessage['vendor_messages']}</p>";
                echo "<p>Timestamp: {$rowMessage['timestamp']}</p>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No messages sent by you yet.</p>";
        }
    } else {
        // Handle the case where the query was not successful
        echo "Error fetching messages: " . $connect->error;
    }
?>

<?php if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])): ?>
    <!-- Display a form for the vendor to submit messages -->
    <form method='post'>
        <h2>Submit a Message</h2>
        <textarea name='vendor_messages' placeholder='Type your message here' rows='4' cols='50'></textarea><br>
        <input type='submit' name='submit_message' value='Submit'>
        <input type='submit' name='back' value='Back' onclick="window.location='vendor_index.php'; return false;">
    </form>
<?php else: ?>
    <?php header("location: vendor_login.php"); ?>
<?php endif; ?>
</div>


</body>
</html>
