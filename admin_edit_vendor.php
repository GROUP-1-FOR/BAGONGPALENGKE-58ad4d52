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
        $sql = "SELECT vendor_name, vendor_first_name, vendor_last_name, vendor_userid, vendor_email, vendor_mobile_number, vendor_product FROM vendor_edit_profile WHERE vendor_userid = ?";
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
                </script>
            </head>

            <body>

                <h2>Edit Vendor</h2>

                <form method="post" action="" onsubmit="return confirmUpdate();">
                    <label for="vendor_name">Vendor Name:</label>
                    <input type="text" id="vendor_name" name="vendor_name" value="<?= $vendor_data['vendor_name'] ?>"readonly><br>

                    <label for="vendor_first_name">First Name:</label>
                    <input type="text" id="vendor_first_name" name="vendor_first_name" value="<?= $vendor_data['vendor_first_name'] ?>"readonly><br>

                    <label for="vendor_last_name">Last Name:</label>
                    <input type="text" id="vendor_last_name" name="vendor_last_name" value="<?= $vendor_data['vendor_last_name'] ?>"readonly><br>

                    <label for="vendor_email">Email:</label>
                    <input type="email" id="vendor_email" name="vendor_email" value="<?= $vendor_data['vendor_email'] ?>"readonly><br>

                    <label for="vendor_mobile_number">Mobile Number:</label>
                    <input type="text" id="vendor_mobile_number" name="vendor_mobile_number" value="<?= $vendor_data['vendor_mobile_number'] ?>"readonly><br>

                    <label for="vendor_product">Product:</label>
                    <input type="text" id="vendor_product" name="vendor_product" value="<?= $vendor_data['vendor_product'] ?>"readonly><br>

                    <input type="submit" value="Update">

                </form>

                <?php
                // Process the form submission
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $new_vendor_name = $_POST['vendor_name'];
                    $new_vendor_first_name = $_POST['vendor_first_name'];
                    $new_vendor_last_name = $_POST['vendor_last_name'];
                    $new_vendor_email = $_POST['vendor_email'];
                    $new_vendor_mobile_number = $_POST['vendor_mobile_number'];
                    $new_vendor_product = $_POST['vendor_product'];

                    // Update vendor_sign_in table
                    $update_vendor_signin_sql = "UPDATE vendor_sign_in SET
                        vendor_name = ?,
                        vendor_first_name = ?,
                        vendor_last_name = ?,
                        vendor_email = ?,
                        vendor_mobile_number = ?,
                        vendor_product = ?
                        WHERE vendor_userid = ?";

                    $update_vendor_signin_stmt = $connect->prepare($update_vendor_signin_sql);
                    $update_vendor_signin_stmt->bind_param("sssssss", $new_vendor_name, $new_vendor_first_name, $new_vendor_last_name, $new_vendor_email, $new_vendor_mobile_number, $new_vendor_product, $vendor_userid);

                    // Update vendor_edit_profile table
                    $update_vendor_profile_sql = "UPDATE vendor_edit_profile SET
                        vendor_edit = 1
                        WHERE vendor_userid = ?";

                    $update_vendor_profile_stmt = $connect->prepare($update_vendor_profile_sql);
                    $update_vendor_profile_stmt->bind_param("s", $vendor_userid);

                    // Execute both statements
                    if ($update_vendor_signin_stmt->execute() && $update_vendor_profile_stmt->execute()) {
                        echo "<p>Vendor information updated successfully.</p>";
                    } else {
                        echo "<p>Error updating vendor sign-in information: " . $update_vendor_signin_stmt->error . "</p>";
                        echo "<p>Error updating vendor edit profile information: " . $update_vendor_profile_stmt->error . "</p>";
                    }

                    $update_vendor_signin_stmt->close();
                    $update_vendor_profile_stmt->close();
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
} else {
    // Redirect if not logged in
    header("location:admin_login.php");
}
?>
