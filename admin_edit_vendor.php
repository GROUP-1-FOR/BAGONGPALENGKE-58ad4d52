<?php
require_once "config.php";

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

// Check if the vendor_userid is provided in the URL
if (isset($_GET['vendor_userid'])) {
    $vendor_userid = $_GET['vendor_userid'];

    // Fetch vendor information from the database
    $sql = "SELECT vendor_name, vendor_first_name, vendor_last_name, vendor_userid, vendor_email, vendor_mobile_number FROM vendor_edit_profile WHERE vendor_userid = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $vendor_userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor_data = $result->fetch_assoc();
    $stmt->close();

    // Check if the vendor data is found
    if ($vendor_data) {
        // Display the vendor information in a form
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Vendor</title>

            <script>
                function confirmUpdate() {
                    return confirm("Are you sure you want to update the vendor information?");
                }

                function confirmRemove() {
                    return confirm("Are you sure you want to remove this edit request?");
                }
            </script>
        </head>

        <body>

            <h2>Edit Vendor</h2>

            <form method="post" action="" onsubmit="return confirmUpdate();">
                <label for="vendor_name">Vendor Name:</label>
                <input type="text" id="vendor_name" name="vendor_name" value="<?= $vendor_data['vendor_name'] ?>" readonly><br>

                <label for="vendor_first_name">First Name:</label>
                <input type="text" id="vendor_first_name" name="vendor_first_name" value="<?= $vendor_data['vendor_first_name'] ?>" readonly><br>

                <label for="vendor_last_name">Last Name:</label>
                <input type="text" id="vendor_last_name" name="vendor_last_name" value="<?= $vendor_data['vendor_last_name'] ?>" readonly><br>

                <label for="vendor_email">Email:</label>
                <input type="email" id="vendor_email" name="vendor_email" value="<?= $vendor_data['vendor_email'] ?>" readonly><br>

                <label for="vendor_mobile_number">Mobile Number:</label>
                <input type="text" id="vendor_mobile_number" name="vendor_mobile_number" value="<?= $vendor_data['vendor_mobile_number'] ?>" readonly><br>

                <input type="submit" name="update" value="Update">

            </form>
            <!-- Add a Remove button -->
            <form method="post" action="">
                <input type="hidden" name="vendor_userid" value="<?= $vendor_userid ?>">
                <input type="submit" name="remove" value="Remove" onclick="return confirmRemove();">
            </form>

            <?php
            // Process the form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['update'])) {
                    $new_vendor_name = $_POST['vendor_name'];
                    $new_vendor_first_name = $_POST['vendor_first_name'];
                    $new_vendor_last_name = $_POST['vendor_last_name'];
                    $new_vendor_email = $_POST['vendor_email'];
                    $new_vendor_mobile_number = $_POST['vendor_mobile_number'];

                    // Insert into vendor_notification table
                    $notifTitle = "Profile Updated!";
                    $editValue = 1; // Set the confirm value to 1
                    $editDate = date('Y-m-d H:i:s');

                    $sqlInsertNotification = "INSERT INTO vendor_notification (vendor_userid, title, edit, notif_date) VALUES (?, ?, ?, ?)";
                    $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
                    $stmtInsertNotification->bind_param('ssis', $vendor_userid, $notifTitle, $editValue, $editDate);
                    $stmtInsertNotification->execute();

                    // Update vendor_sign_in table
                    $update_vendor_signin_sql = "UPDATE vendor_sign_in SET
                        vendor_name = ?,
                        vendor_first_name = ?,
                        vendor_last_name = ?,
                        vendor_email = ?,
                        vendor_mobile_number = ?
                        WHERE vendor_userid = ?";

                    $update_vendor_signin_stmt = $connect->prepare($update_vendor_signin_sql);
                    $update_vendor_signin_stmt->bind_param("ssssss", $new_vendor_name, $new_vendor_first_name, $new_vendor_last_name, $new_vendor_email, $new_vendor_mobile_number, $vendor_userid);

                    // Update admin_messages table
                    $update_admin_messages_sql = "UPDATE admin_messages SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_admin_messages_stmt = $connect->prepare($update_admin_messages_sql);
                    $update_admin_messages_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Update vendor_messages table
                    $update_vendor_messages_sql = "UPDATE vendor_messages SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_vendor_messages_stmt = $connect->prepare($update_vendor_messages_sql);
                    $update_vendor_messages_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Update paid_records table
                    $update_paid_records_sql = "UPDATE paid_records SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_paid_records_stmt = $connect->prepare($update_paid_records_sql);
                    $update_paid_records_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);
                    // Update archive_records table
                    $update_archive_records_sql = "UPDATE archive_records SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_archive_records_stmt = $connect->prepare($update_archive_records_sql);
                    $update_archive_records_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Update vendor_balance table
                    $update_vendor_balance_sql = "UPDATE vendor_balance SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_vendor_balance_stmt = $connect->prepare($update_vendor_balance_sql);
                    $update_vendor_balance_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Update admin_stall_map table
                    $update_admin_stall_map_sql = "UPDATE admin_stall_map SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_admin_stall_map_stmt = $connect->prepare($update_admin_stall_map_sql);
                    $update_admin_stall_map_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Update ven_payments table
                    $update_ven_payments_sql = "UPDATE ven_payments SET
                        vendor_name = ?
                        WHERE vendor_userid = ?";

                    $update_ven_payments_stmt = $connect->prepare($update_ven_payments_sql);
                    $update_ven_payments_stmt->bind_param("ss", $new_vendor_name, $vendor_userid);

                    // Delete row from vendor_edit_profile table
                    $delete_vendor_profile_sql = "DELETE FROM vendor_edit_profile WHERE vendor_userid = ?";
                    $delete_vendor_profile_stmt = $connect->prepare($delete_vendor_profile_sql);
                    $delete_vendor_profile_stmt->bind_param("s", $vendor_userid);

                    // Execute statements
                    if (
                        $update_vendor_signin_stmt->execute() &&
                        $update_admin_messages_stmt->execute() &&
                        $update_vendor_messages_stmt->execute() &&
                        $update_paid_records_stmt->execute() &&
                        $update_archive_records_stmt->execute() &&
                        $update_vendor_balance_stmt->execute() &&
                        $update_admin_stall_map_stmt->execute() &&
                        $update_ven_payments_stmt->execute() &&
                        $delete_vendor_profile_stmt->execute()
                    ) {
                        echo "<p>Vendor information updated successfully.</p>";

                        // Redirect to admin_vendor_manage_accounts.php
                        header("Location: admin_vendor_manage_accounts.php");
                        exit();
                    } else {
                        echo "<p>Error updating vendor sign-in information: " . $update_vendor_signin_stmt->error . "</p>";
                        echo "<p>Error updating admin messages: " . $update_admin_messages_stmt->error . "</p>";
                        echo "<p>Error updating vendor messages: " . $update_vendor_messages_stmt->error . "</p>";
                        echo "<p>Error updating paid records: " . $update_paid_records_stmt->error . "</p>";
                        echo "<p>Error updating archive records: " . $update_archive_records_stmt->error . "</p>";
                        echo "<p>Error updating vendor balance: " . $update_vendor_balance_stmt->error . "</p>";
                        echo "<p>Error updating admin stall map: " . $update_admin_stall_map_stmt->error . "</p>";
                        echo "<p>Error updating admin stall map: " . $update_ven_payments_stmt->error . "</p>";
                        echo "<p>Error deleting vendor profile information: " . $delete_vendor_profile_stmt->error . "</p>";
                    }

                    // Close prepared statements
                    $update_vendor_signin_stmt->close();
                    $update_admin_messages_stmt->close();
                    $update_vendor_messages_stmt->close();
                    $update_paid_records_stmt->close();
                    $update_archive_records_stmt->close();
                    $update_vendor_balance_stmt->close();
                    $update_admin_stall_map_stmt->close();
                    $update_ven_payments_stmt->close();
                    $delete_vendor_profile_stmt->close();
                } elseif (isset($_POST['remove'])) {

                    // Insert into vendor_notification table
                    $notifTitle = "Edit Request Removed";
                    $editValue = 1; // Set the confirm value to 1
                    $editDate = date('Y-m-d H:i:s');

                    $sqlInsertNotification = "INSERT INTO vendor_notification (vendor_userid, title, edit, notif_date) VALUES (?, ?, ?, ?)";
                    $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
                    $stmtInsertNotification->bind_param('ssis', $vendor_userid, $notifTitle, $editValue, $editDate);
                    $stmtInsertNotification->execute();
                    // Remove logic
                    $delete_vendor_profile_sql = "DELETE FROM vendor_edit_profile WHERE vendor_userid = ?";
                    $delete_vendor_profile_stmt = $connect->prepare($delete_vendor_profile_sql);
                    $delete_vendor_profile_stmt->bind_param("s", $vendor_userid);

                    if ($delete_vendor_profile_stmt->execute()) {
                        echo "<p>Vendor information removed successfully.</p>";
                        // Redirect using JavaScript
                        echo '<script>window.location.href = "admin_vendor_manage_accounts.php";</script>';
                    } else {
                        echo "<p>Error removing vendor profile information: " . $delete_vendor_profile_stmt->error . "</p>";
                    }

                    $delete_vendor_profile_stmt->close();
                }
            }
            ?>

        </body>
        <button><a href="admin_vendor_manage_accounts.php">Back</a></button>

        </html>
<?php
    } else {
        // Vendor not found
        echo "Vendor not found.";
    }
} else {
    // Vendor ID not provided in the URL
    echo "Vendor ID not provided.";
}
// Close the database connection
$connect->close();
