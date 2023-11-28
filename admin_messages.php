<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendorName = $_SESSION["userid"];

    // Fetch the list of admin names for the dropdown
    $sqlAdminNames = "SELECT admin_name FROM admin_sign_in";
    $resultAdminNames = $connect->query($sqlAdminNames);

    // Process form submission when a message is sent
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $receiver = $_POST["receiver"];
        $message = $_POST["message"];

        // Validate and sanitize input if needed

        // Insert the message into the database
        $sqlInsertMessage = "INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('sss', $vendorName, $receiver, $message);
        $stmtInsertMessage->execute();
    }

    // Fetch and display messages for the logged-in vendor
    $sqlGetMessages = "SELECT * FROM messages WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY timestamp DESC";
    $stmtGetMessages = $connect->prepare($sqlGetMessages);
    $stmtGetMessages->bind_param('ssss', $vendorName, $receiver, $receiver, $vendorName);
    $stmtGetMessages->execute();
    $resultGetMessages = $stmtGetMessages->get_result();

    // Debugging: Check if the query executed successfully
    if (!$resultGetMessages) {
        die("Query failed: " . $stmtGetMessages->error);
    }

    // Output the SQL Query for debugging
    // echo "SQL Query: $sqlGetMessages<br>";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Messages Page</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            margin: 20px;
        }

        h1, h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #850F16;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #EEEAD6
        }

        #messages {
            margin-top: 30px;
            text-align: left;
        }

        .message {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        a {
            text-decoration: none;
            color: #333;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <h1>Welcome, <?php echo $vendorName; ?>!</h1>
    <h2>Send a Message</h2>
    <form method="post">
        <label for="receiver">Select Receiver:</label>
        <select name="receiver" required>
            <?php
            while ($row = $resultAdminNames->fetch_assoc()) {
                echo "<option value='" . $row["admin_name"] . "'>" . $row["admin_name"] . "</option>";
            }
            ?>
        </select>
        <br>

        <!-- Display messages if needed -->
        <div id="messages">
            <h2>Messages</h2>
            <!-- Messages are displayed here -->
            <?php
                // Display messages
                while ($rowMessage = $resultGetMessages->fetch_assoc()) {
                    echo "<div class='message'><strong>From: " . $rowMessage["sender"] . "</strong> To: " . $rowMessage["receiver"] . "<br>";
                    echo $rowMessage["message"] . "<br>";
                    echo "Sent at: " . $rowMessage["timestamp"] . "</div>";
                }
            ?>
        </div>

        <br>
        <label for="message">Message:</label>
        <textarea name="message" rows="4" cols="50" required></textarea>
        <br>
        <button type="submit">Send Message</button>
    </form>
    <br>
    <a href="vendor_main_page.php">Back to Main Page</a>
    <br>
    <a href="vendor_logout.php">Logout</a>
</body>

</html>

<?php
} else {
    // Redirect to the login page if the user is not logged in
    header("location: vendor_login.php");
}
?>