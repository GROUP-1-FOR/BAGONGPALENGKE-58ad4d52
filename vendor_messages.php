<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch vendor data from vendor_sign_in table
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
        // Handle the case where the vendor data is not found
        die("Vendor data not found.");
    }

    // Process message submission
    if (isset($_POST['submit_message'])) {
        $userMessage = $_POST['user_message'];
        $timestamp = date('Y-m-d H:i:s');

        // Insert user's message into system_messages table
        $sqlInsertMessage = "INSERT INTO system_messages (vendor_name, vendor_stall_number, vendor_messages, vendor_timestamp) VALUES (?, ?, ?, ?)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('ssss', $vendorName, $vendorStallNumber, $userMessage, $timestamp);
        $stmtInsertMessage->execute();
    }

    // Fetch messages from system_messages table
    $sqlFetchMessages = "SELECT * FROM system_messages WHERE (vendor_name = ? AND vendor_stall_number = ?) OR (vendor_name = 'admin' AND vendor_stall_number = 'admin') ORDER BY COALESCE(admin_timestamp, vendor_timestamp)";
    $stmtFetchMessages = $connect->prepare($sqlFetchMessages);
    $stmtFetchMessages->bind_param('ss', $vendorName, $vendorStallNumber);
    $stmtFetchMessages->execute();
    $resultFetchMessages = $stmtFetchMessages->get_result();

    // Display messages
    ?>
  <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Messages</title>
    <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        h1, h2 {
            color: #850F16;
        }

        div {
            margin: auto;
            width: 50%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #850F16;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        p {
            margin-bottom: 10px;
        }

        a {
            display: block;
            margin-top: 20px;
            color: #850F16;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Welcome, <?php echo $vendorName; ?>!</h1>
    <h2>Vendor Stall Number: <?php echo $vendorStallNumber; ?></h2>

    <div>
        <!-- Form for submitting messages -->
        <form method="post">
            <label for="user_message">Your Message:</label>
            <textarea name="user_message" id="user_message" rows="4" cols="50" required></textarea>
            <br>
            <button type="submit" name="submit_message">Send Message</button>
        </form>

        <!-- Display messages -->
        <?php while ($rowMessage = $resultFetchMessages->fetch_assoc()) : ?>
            <p>
                <?php
                if ($rowMessage['vendor_name'] == 'admin') {
                    echo "Admin: " . $rowMessage['admin_reply'];
                } else {
                    echo "You: " . $rowMessage['vendor_messages'];
                }
                ?>
            </p>
        <?php endwhile; ?>
    </div>

    <a href="vendor_main_page.php">Go back to Main Page</a>
</body>

</html>
<?php
} else {
    // Redirect to the login page if the user is not logged in
    header("location:vendor_login.php");
}
?>
