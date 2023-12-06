<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if (isset($_GET['stall_number'])) {
        $stallNumber = $_GET['stall_number'];
        // Debug statement
        echo "Stall Number: " . $stallNumber;

        // Display the form for adding stall information
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stall</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
    <h2>Add Stall</h2>
    <form action="handle_stall_form.php" method="post">
        <input type="hidden" name="stall_number" value="<?= $stallNumber ?>">
        <label for="vendor_name">Vendor Name:</label>
        <input type="text" name="vendor_name" required><br>

        <label for="vendor_userid">Vendor User ID:</label>
        <input type="text" name="vendor_userid" required><br>

        <label for="balance">Balance:</label>
        <input type="text" name="balance" required><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
<?php
    }
}
?>
