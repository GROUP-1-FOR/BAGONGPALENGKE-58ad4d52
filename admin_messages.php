<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Get the selected vendor from the URL parameter
    $selectedVendor = isset($_GET['vendor']) ? $_GET['vendor'] : null;

    if ($selectedVendor) {
        // Fetch messages for the selected vendor
        $sqlFetchMessages = "
            SELECT *
            FROM system_messages
            WHERE (vendor_name = ? AND vendor_stall_number = ?) OR (vendor_name = 'admin' AND vendor_stall_number = 'admin')
            ORDER BY COALESCE(admin_timestamp, vendor_timestamp)
        ";
        $stmtFetchMessages = $connect->prepare($sqlFetchMessages);
        $stmtFetchMessages->bind_param('ss', $selectedVendor, $selectedVendor);
        $stmtFetchMessages->execute();
        $resultFetchMessages = $stmtFetchMessages->get_result();

        // Display the messages
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Messages</title>
            <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        h1, h2, h3 {
            color: #850F16;
        }

        .container {
            margin: auto;
            width: 50%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-box {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }

        p {
            margin: 10px 0;
        }

        form {
            margin-top: 20px;
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
            <div class="container">
                <h1>Welcome, <?php echo $admin_userid; ?>!</h1>
                <h2>Messages for Vendor: <?php echo $selectedVendor; ?></h2>

                <?php
                while ($rowMessage = $resultFetchMessages->fetch_assoc()) {
                    $messageType = ($rowMessage['vendor_name'] == 'admin') ? 'Admin Reply' : 'Vendor Message';
                ?>
                    <div class="message-box">
                        <h3><?php echo $messageType; ?></h3>
                        <p><?php echo $messageType == 'Admin Reply' ? $rowMessage['admin_reply'] : $rowMessage['vendor_messages']; ?></p>
                        <p>Timestamp: <?php echo $rowMessage['vendor_timestamp']; ?></p>
                    </div>
                <?php
                }
                ?>

                <!-- Form for replying to messages -->
                <form method="post">
                    <label for="admin_reply">Your Reply:</label>
                    <textarea name="admin_reply" id="admin_reply" rows="4" cols="50" required></textarea>
                    <br>
                    <button type="submit" name="submit_reply">Send Reply</button>
                </form>

                <a href="admin_messages_preview.php">Back to Messages Preview</a>
            </div>
        </body>

        </html>

        <?php
        // Process form submission
        if (isset($_POST['submit_reply'])) {
            $adminReply = $_POST['admin_reply'];

            // Insert admin reply into the database
            $sqlInsertReply = "
                INSERT INTO system_messages (vendor_name, vendor_stall_number, admin_reply, admin_timestamp)
                VALUES ('admin', 'admin', ?, CURRENT_TIMESTAMP)
            ";
            $stmtInsertReply = $connect->prepare($sqlInsertReply);
            $stmtInsertReply->bind_param('s', $adminReply);
            $stmtInsertReply->execute();

            // Redirect to the current page to avoid form resubmission on page refresh
            header("Location: admin_messages.php?vendor=$selectedVendor");
            exit();
        }
    } else {
        // Handle the case where no vendor is selected
        echo "<p>No vendor selected.</p>";
    }
} else {
    header("location: admin_login.php");
}
?>