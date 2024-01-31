<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Fetch admin_name from the database
    $admin_info_query = "SELECT admin_name FROM admin_sign_in WHERE admin_id = '$admin_id' LIMIT 1";
    $admin_info_result = $connect->query($admin_info_query);

    if ($admin_info_result->num_rows > 0) {
        $admin_info = $admin_info_result->fetch_assoc();
        $admin_name = $admin_info['admin_name'];

        // Store admin_name in the session
        $_SESSION["admin_name"] = $admin_name;
    }

    // Check if the vendor_name and vendor_stall_number are set in the URL
    if (isset($_GET['vendor_name']) && isset($_GET['vendor_stall_number'])) {
        $recipient = $_GET['vendor_name'];
        $stall_number = $_GET['vendor_stall_number'];

        // Fetch the vendor_userid based on vendor_name and vendor_stall_number
        $vendor_userid_query = "SELECT vendor_userid FROM vendor_sign_in WHERE vendor_name = '$recipient' AND vendor_stall_number = '$stall_number' LIMIT 1";
        $vendor_userid_result = $connect->query($vendor_userid_query);

        if ($vendor_userid_result->num_rows > 0) {
            $vendor_userid_row = $vendor_userid_result->fetch_assoc();
            $vendor_userid = $vendor_userid_row['vendor_userid'];
        } else {
            // Handle the case where vendor_userid is not found
            die("Error: Vendor userid not found.");
        }

        // Fetch messages for the selected vendor, both vendor and admin messages
        $messages_query = "
        SELECT 'vendor' as message_type, vendor_chat as message, vendor_timestamp as timestamp
        FROM vendor_messages
        WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$recipient' AND vendor_stall_number = '$stall_number'
        
        UNION ALL
        
        SELECT 'admin' as message_type, admin_reply as message, admin_timestamp as timestamp
        FROM admin_messages
        WHERE vendor_userid = '$vendor_userid' AND vendor_name = '$recipient' AND vendor_stall_number = '$stall_number'
        
        ORDER BY timestamp ASC";

        // Execute the query and handle errors
        $messages_result = $connect->query($messages_query);
        if (!$messages_result) {
            die("Error executing the query: " . $connect->error);
        }

?>

        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SIGN IN</title>
            <link rel="stylesheet" type="text/css" href="index.css">
            <link rel="stylesheet" type="text/css" href="text-style.css">
            <link rel="javascript" type="text/script" href="js-style.js">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

            <style>
                body {
                    margin: 0;
                    color: white;
                }

                .messaging-container {
                    display: flex;
                    height: 100vh;
                    width: 1250px;
                }

                p {
                    font-family: Arial, Helvetica, sans-serif;
                }

                .admin-panel,
                .vendor-panel {
                    flex: 1;
                    max-width: 300px;
                    padding: 20px;
                    background-color: #f0f0f0;
                }

                .vendor-panel {
                    border-left: 1px solid #ccc;
                }

                .conversation-panel {
                    flex: 3;
                    display: flex;
                    flex-direction: column;
                    padding: 20px;
                }

                .message-input {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 10px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }

                .admin-panel .message-input {
                    border-color: #ccc;
                }

                .vendor-panel .message-input {
                    border-color: #ddd;
                }

                .message {
                    margin-bottom: 10px;
                    width: 1000px;
                    padding: 10px;
                    border-radius: 5px;
                }

                .admin-message {
                    margin-top: 20px;
                    width: 500px;
                    align-self: flex-end;
                    background-color: #9C3D42;
                    color: white;

                    /* Light blue for admin */
                }

                .vendor-message {
                    color: white;
                    margin-top: 20px;
                    width: 500px;
                    align-self: flex-start;
                    background-color: #7A7D7C;
                    /* Pink for vendor */
                }
            </style>


        </head>

        <body>
            <header></header>
            <?php include 'sidebar.php'; ?>

            <div class="flex-row-body">
                <div class="message-back-header">
                    <a class="back-button7" href='admin_messages_preview.php'>
                        <img class="back-icon" src="assets\images\sign-in\back-icon.svg"></a>
                    <h2 class="message-header-v2">Messages</h2>
                </div>
                <div class="recepient-panel">

                </div>
                <div class="hr"></div>


                <div class="recipient-box">
                    <label class="recipient" for="recipient">From: <?php echo $recipient; ?></label>
                    <p class="message-datetime">December 25 | 10:30 PM</p>
                </div>

                <!-- <div class="convo-container"> -->
                <div class="text-messages">

                    <div class="message-container2">
                        <?php
                        while ($message_row = $messages_result->fetch_assoc()) {
                            $message_type = ucfirst($message_row['message_type']);
                            $message_text = $message_row['message'];
                            $message_timestamp = $message_row['timestamp'];
                            $messageClass = ($message_type == 'Admin') ? 'admin-message' : 'vendor-message';

                            echo "<div class='message $messageClass'>";
                            echo "<p>$message_text</p>";
                            echo "<p class='time-stamp'>Timestamp: $message_timestamp</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <!-- </div> -->
                <div class="background">
                    <form class="flex-box-row-send" action="process_admin_reply.php" method="post">
                        <textarea class="text-area2" name="admin_reply" id="admin_reply" placeholder="Type your text message here..." required></textarea>
                        <input type="hidden" name="admin_name" value="<?php echo $_SESSION["admin_name"]; ?>">
                        <input type="hidden" name="recipient" value="<?php echo $recipient; ?>">
                        <input type="hidden" name="stall_number" value="<?php echo $stall_number; ?>">
                        <button class="send-button2" type="submit">Reply</button>
                    </form>
                </div>
                <script>
                    function sendMessage(inputId, sender) {
                        var messageInput = document.getElementById(inputId);
                        var messageText = messageInput.value.trim();

                        if (messageText !== "") {
                            var conversationPanel = document.getElementById("conversation-panel");
                            var messageDiv = document.createElement("div");
                            var messageClass = sender === "admin" ? "admin-message" : "vendor-message";

                            messageDiv.className = "message " + messageClass;
                            messageDiv.innerHTML = "<p>" + messageText + "</p>";
                            conversationPanel.appendChild(messageDiv);

                            // Clear the input after sending the message
                            messageInput.value = "";
                        }
                    }
                </script>
                <footer></footer>

            </div>


        </body>

        </html>
<?php
    } else {
        // Redirect to messages preview if vendor_name or vendor_stall_number is not set
        header("location:admin_messages_preview.php");
    }
} else {
    header("location:admin_logout.php");
}
?>