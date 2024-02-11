<?php
require_once "config.php";

// Check if the user is logged in
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

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
            // Process the form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['update'])) {
                    // Handle update logic
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
                    // Handle remove logic
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

            // Display the vendor information in a form
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" type="text/css" href="index.css">
                <link rel="stylesheet" type="text/css" href="text-style.css">
                <link rel="stylesheet" type="text/css" href="text-positions.css">
                <link rel="javascript" type="text/script" href="js-style.js">
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

                <script>
                    function confirmUpdate() {
                        var confirmed = confirm("Are you sure you want to update the vendor information?");
                        return confirmed;
                    }

                    // Function to display a notification
                    function showNotification(message) {
                        if (window.Notification && Notification.permission === "granted") {
                            new Notification(message);
                        } else if (window.Notification && Notification.permission !== "denied") {
                            Notification.requestPermission().then(function(permission) {
                                if (permission === "granted") {
                                    new Notification(message);
                                }
                            });
                        }
                    }

                    function confirmRemove() {
                        return confirm("Are you sure you want to remove this edit request?");
                    }

                    // function confirmUpdate() {
                    //     var confirmed = confirm("Are you sure you want to update the vendor information?");
                    //     if (confirmed) {
                    //         // Notify user if the update is confirmed
                    //         if (window.Notification && Notification.permission === "granted") {
                    //             new Notification("Vendor information updated successfully.");
                    //         } else if (window.Notification && Notification.permission !== "denied") {
                    //             Notification.requestPermission().then(function(permission) {
                    //                 if (permission === "granted") {
                    //                     new Notification("Vendor information updated successfully.");
                    //                 }
                    //             });
                    //         }
                    //     }
                    //     return confirmed;
                    // }

                    // function confirmRemove() {
                    //     return confirm("Are you sure you want to remove this edit request?");
                    // }
                </script>
            </head>

            <body>

                <header class="header2"></header>
                <?php include 'sidebar2.php'; ?>
                <br>
                <br>
                <div class="flex-row">
                    <h2 class="manage-account-header">EDIT VENDOR</h2>
                    <div class="report-manage">

                        <form method="post" action="" onsubmit="return confirmUpdate();">
                            <div>
                                <!-- FIRST BOX -->
                                <div class="flex-row-direction">
                                    <div class="box-position2">
                                        <div class="flexbox-column">
                                            <label class="title-label tl1" for="vendor_name">Vendor Name:</label>
                                            <input class="input-info input-info-margin" type="text" id="vendor_name" name="vendor_name" value="<?= $vendor_data['vendor_name'] ?>" readonly><br>

                                            <label class="title-label tl1" for="vendor_first_name">First Name:</label>
                                            <input class="input-info input-info-margin" type="text" id="vendor_first_name" name="vendor_first_name" value="<?= $vendor_data['vendor_first_name'] ?>" readonly><br>

                                            <label class="title-label tl1" for="vendor_last_name">Last Name:</label>
                                            <input class="input-info input-info-margin" type="text" id="vendor_last_name" name="vendor_last_name" value="<?= $vendor_data['vendor_last_name'] ?>" readonly><br>
                                        </div>
                                    </div>
                                    <div class="box-position3">
                                        <label class="title-label tl1" for="vendor_email">Email:</label>
                                        <input class="input-info" type="email" id="vendor_email" name="vendor_email" value="<?= $vendor_data['vendor_email'] ?>" readonly><br>

                                        <label class="title-label tl1" for="vendor_mobile_number">Mobile Number:</label>
                                        <input class="input-info" type="text" id="vendor_mobile_number" name="vendor_mobile_number" value="<?= $vendor_data['vendor_mobile_number'] ?>" readonly><br>

                                        <input class="submit-btn2" type="submit" name="update" value="Update">
                                        <!-- Add a Remove button -->
                                        <form method="post" action="">
                                            <input type="hidden" name="vendor_userid" value="<?= $vendor_userid ?>">
                                            <input class="submit-btn3" type="submit" name="remove" value="Remove" onclick="return confirmRemove();">
                                        </form>
                                    </div>
                        </form>


                    </div>
                </div>
    <?php
        } else {
            // Vendor not found
            echo "Vendor not found.";
        }
    } else {
        // Vendor ID not provided in the URL
        echo "Vendor ID not provided.";
    }
} else {
    // Redirect if not logged in
    header("location:admin_logout.php");
}

// Close the database connection
$connect->close();
    ?>

    <!-- This part should be outside PHP block -->
    </div>
    </div>
            </body>
            <!-- <button><a href="admin_vendor_manage_accounts.php">Back</a></button> -->

            </html>