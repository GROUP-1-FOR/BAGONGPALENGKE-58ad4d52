<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    // Include your existing code for session validation

    // Fetch admin name from admin_sign_in table
    $adminUserId = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

$sqlFetchAdminName = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = ?";
$stmtFetchAdminName = $connect->prepare($sqlFetchAdminName);
$stmtFetchAdminName->bind_param('s', $adminUserId);
$stmtFetchAdminName->execute();
$resultFetchAdminName = $stmtFetchAdminName->get_result();

if ($resultFetchAdminName->num_rows > 0) {
    $rowAdminName = $resultFetchAdminName->fetch_assoc();
    $adminName = $rowAdminName['admin_name'];
} else {
    // Handle the case where the admin name is not found or there's an issue with the database query
    die("Admin name not found or database query issue.");
}

// Fetch vendors who have not been contacted yet
$query = "SELECT vendor_name, vendor_stall_number FROM vendor_sign_in 
              WHERE vendor_userid NOT IN (
                  SELECT vendor_userid FROM admin_messages
                  UNION
                  SELECT vendor_userid FROM vendor_messages
              )";

// Execute the query and handle errors
$result = $connect->query($query);
if (!$result) {
    die("Error executing the query: " . $connect->error);
}

// Fetch all vendors who have not been contacted yet into an array
$vendors = [];
while ($row = $result->fetch_assoc()) {
    $vendors[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Message</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <script>
        // Function to enable or disable the "Send Message" button based on the message content and vendor list
        function toggleSendMessageButton() {
            var messageText = document.getElementById('message_text').value;
            var sendMessageButton = document.getElementById('send_message_button');
            var vendorList = <?php echo json_encode($vendors); ?>;

            // Enable the button if the message is not empty and there are vendors in the list, otherwise disable it
            sendMessageButton.disabled = (messageText.trim() === '' || vendorList.length === 0);
        }
    </script>
</head>

<body>
    <header></header>
    <?php include 'sidebar.php'; ?>

    <div class="flex-row-body">
        <div class="message-back-header">
            <a class="back-button7" href='admin_index.php'>
                <img class="back-icon" src="assets\images\sign-in\back-icon.svg"></a>
            <h2 class="message-header-v2">Create New Message</h2>
        </div>
        <div class="recepient-panel">

        </div>
        <div class="hr"></div>

        <form method="post" action="process_admin_createnew_message.php">
            <div class="recipient-box">
                <label class="recipient" for="recipient">Select Recipient:</label>
                <select class="recipient-dropdown" name="recipient" id="recipient">
                    <?php
                    // Display the list of vendors in the dropdown
                    foreach ($vendors as $vendor) {
                        echo "<option value='" . $vendor['vendor_name'] . "-" . $vendor['vendor_stall_number'] . "'>" . $vendor['vendor_name'] . " - Stall " . $vendor['vendor_stall_number'] . "</option>";
                    }
                    ?>
                </select>
                <p class="message-datetime">December 25 | 10:30 PM</p>
            </div>
            <div class="convo-container">
                <div class="text-messages">
                </div>

                <div class="background">
                    <div class="flex-box-row-send">
                        <br>
                        <textarea class="text-area" name="message_text" id="message_text" rows="4" cols="50" placeholder="Type your message here" oninput="toggleSendMessageButton()"></textarea>
                        <!-- Hidden input to send adminName along with the form -->
                        <input type="hidden" name="admin_name" value="<?php echo $adminName; ?>">
                        <!-- "Send Message" button with the id 'send_message_button' -->
                        <button class="send-button2" type="submit" name="send_message" id="send_message_button" disabled> Send </button>
                    </div>
                </div>
        </form>
    </div>

    <footer></footer>

    </div>


</body>

</html>