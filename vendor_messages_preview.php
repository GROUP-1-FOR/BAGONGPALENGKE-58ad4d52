<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    // Fetch vendor_name from vendor_sign_in based on vendor_id
    $vendorNameQuery = "SELECT vendor_name FROM vendor_sign_in WHERE vendor_id = '$id'";
    $vendorNameResult = mysqli_query($connect, $vendorNameQuery);

    // Check if the query was successful and data is present
    if ($vendorNameResult && mysqli_num_rows($vendorNameResult) > 0) {
        $vendorRow = mysqli_fetch_assoc($vendorNameResult);
        $sender_vendor = $vendorRow['vendor_name'];
    } else {
        // Handle the case where the vendor_name is not found
        // You can redirect to an error page or display an error message
        echo "Error: Unable to fetch vendor_name.";
        exit;
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Preview</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo $sender_vendor?>!</h1>
    </header>
    <h2>Preview Messages:</h2>
    
    <?php
    // Display preview messages from the database
    $selectQuery = "SELECT * FROM messages WHERE sender_vendor = '$sender_vendor' OR receiver_vendor = '$sender_vendor' ORDER BY timestamp DESC";
    $result = mysqli_query($connect, $selectQuery);

    while ($row = mysqli_fetch_assoc($result)) {
        $from = ($sender_vendor == $row['sender_vendor']) ? $sender_vendor : $row['sender_admin'];
        $to = ($sender_vendor == $row['sender_vendor']) ? $row['receiver_admin'] : $sender_vendor;

        echo "<p>From: " . $from . "<br>To: " . $to . "<br>Message: " . $row['message'] . "<br>Timestamp: " . $row['timestamp'] . "</p>";
    }
    ?>
    <a href="vendor_messages.php">
        <h1>Go to Full Messages</h1>
    </a>
</body>

</html>

<?php
} else {
    header("location:vendor_login.php");
}
?>