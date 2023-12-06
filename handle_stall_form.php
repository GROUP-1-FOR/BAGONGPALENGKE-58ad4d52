<!-- handle_stall_form.php -->
<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stallNumber = $_POST["stall_number"];
        $vendorName = $_POST["vendor_name"];
        $vendorUserId = $_POST["vendor_userid"];
        $balance = $_POST["balance"];

        // Perform database insertion and update
        // Make sure to sanitize and validate user inputs before inserting into the database

        // Example query (replace with your actual query)
        $query = "INSERT INTO admin_stall_map (vendor_stall_number, vendor_name, vendor_userid, balance, vacant) VALUES ('$stallNumber', '$vendorName', '$vendorUserId', '$balance', 1)";

        // Execute the query and handle the result

        // Redirect to the main page or show a success message
        header("Location: try.php");
        exit();
    }
}
?>