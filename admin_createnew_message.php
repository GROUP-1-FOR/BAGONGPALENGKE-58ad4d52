<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {

    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

$sqlFetchAdminName = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = ?";
$stmtFetchAdminName = $connect->prepare($sqlFetchAdminName);
$stmtFetchAdminName->bind_param('s', $admin_userid);
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
    <title>Create New Message</title>
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
    <h1>Create New Message</h1>

    <!-- Form to send a new message -->
    <form method="post" action="process_admin_createnew_message.php">
        <label for="recipient">Select Recipient:</label>
        <select name="recipient" id="recipient">
            <?php
            // Display the list of vendors in the dropdown
            foreach ($vendors as $vendor) {
                echo "<option value='" . $vendor['vendor_name'] . "-" . $vendor['vendor_stall_number'] . "'>" . $vendor['vendor_name'] . " - Stall " . $vendor['vendor_stall_number'] . "</option>";
            }
            ?>
        </select>
        <br>

        <label for="message_text">Message:</label>
        <textarea name="message_text" id="message_text" rows="4" cols="50" placeholder="Type your message here" oninput="toggleSendMessageButton()"></textarea>
        <br>

        <!-- Hidden input to send adminName along with the form -->
        <input type="hidden" name="admin_name" value="<?php echo $adminName; ?>">

        <!-- "Send Message" button with the id 'send_message_button' -->
        <button type="submit" name="send_message" id="send_message_button" disabled>Send Message</button>
    </form>

    <!-- Back button -->
    <a href='admin_messages_preview.php'><button>Back</button></a>
</body>

</html>