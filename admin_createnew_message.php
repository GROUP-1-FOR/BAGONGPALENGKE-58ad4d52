<?php
require("config.php");

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    // Include your existing code for session validation

    // Fetch admin name from admin_sign_in table
    $adminUserId = $_SESSION["userid"];

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
              WHERE CONCAT(vendor_name, '-', vendor_stall_number) NOT IN (
                  SELECT CONCAT(vendor_name, '-', vendor_stall_number) FROM admin_messages
                  UNION
                  SELECT CONCAT(vendor_name, '-', vendor_stall_number) FROM vendor_messages
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
            <textarea name="message_text" rows="4" cols="50" placeholder="Type your message here"></textarea>
            <br>

            <!-- Hidden input to send adminName along with the form -->
            <input type="hidden" name="admin_name" value="<?php echo $adminName; ?>">

            <button type="submit" name="send_message">Send Message</button>
        </form>

        <!-- Back button -->
        <a href='admin_messages_preview.php'><button>Back</button></a>
    </body>

    </html>

<?php
} else {
    header("location:admin_login.php");
}
?>
