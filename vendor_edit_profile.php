<?php
// vendor_edit_profile.php

require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch user data using prepared statement
    $sqlUserData = "SELECT * FROM vendor_sign_in WHERE vendor_userid = ?";
    $stmtUserData = $connect->prepare($sqlUserData);
    $stmtUserData->bind_param('s', $userid); // Use 's' for VARCHAR
    $stmtUserData->execute();
    $resultUserData = $stmtUserData->get_result();

    if ($resultUserData->num_rows > 0) {
        $rowUserData = $resultUserData->fetch_assoc();
        $vendorName = $rowUserData['vendor_name'];
        $email = $rowUserData['vendor_email'];
        $stallNumber = $rowUserData['vendor_stall_number'];
        $mobileNumber = $rowUserData['vendor_mobile_number'];
        $product = $rowUserData['vendor_product'];
    } else {
        // Handle the case where the user ID is not found or there's an issue with the database query
        die("User not found or database query issue.");
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>

<body>
    <h1>Edit Profile</h1>

    <form method="post" action="process_edit_profile.php">
        <label for="vendorName">Vendor Name:</label>
        <input type="text" name="vendorName" value="<?php echo $vendorName; ?>" readonly><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>"><br>

        <label for="stallNumber">Stall Number:</label>
        <input type="text" name="stallNumber" value="<?php echo $stallNumber; ?>" readonly><br>

        <label for="mobileNumber">Mobile Number:</label>
        <input type="text" name="mobileNumber" value="<?php echo $mobileNumber; ?>"><br>

        <label for="product">Product:</label>
        <input type="text" name="product" value="<?php echo $product; ?>"><br>

        <button type="submit" name="requestEdit">Request Edit</button>
    </form>

</body>

</html>

<?php
} else {
    header("location: vendor_login.php");
}
?>