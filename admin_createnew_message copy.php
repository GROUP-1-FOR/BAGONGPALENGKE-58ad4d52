<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Include database connection or functions
    // Example: include('db_connection.php');


    // Fetch vendor messages with the latest message for each vendor
    $query = "SELECT vendor_userid, vendor_name, vendor_stall_number, MAX(latest_timestamp) as latest_timestamp FROM (
                    SELECT vendor_userid, vendor_name, vendor_stall_number, vendor_timestamp as latest_timestamp
                    FROM vendor_messages
                    UNION
                    SELECT vendor_userid, vendor_name, vendor_stall_number, admin_timestamp as latest_timestamp
                    FROM admin_messages
                 ) as combined_messages
                 GROUP BY vendor_userid, vendor_name, vendor_stall_number
                 ORDER BY latest_timestamp DESC";



    $sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";

    // Execute the query
    $result = $connect->query($sql);
    $admin_name = "";
    $admin_name_error = "";

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            $admin_name = $row['admin_name'];
        }
    } else {
        $admin_name_error = "No results found for user ID $admin_userId";
    }

    // Execute the query and handle errors
    $result = $connect->query($query);
    if (!$result) {
        die("Error executing the query: " . $connect->error);
    }

?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SIGN IN</title>
        <link rel="stylesheet" type="text/css" href="index.css">
        <link rel="stylesheet" type="text/css" href="text-style.css">
        <link rel="javascript" type="text/script" href="js-style.js">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    </head>

    <body>
        <header></header>
        <?php include 'sidebar.php'; ?>

        <div class="flex-row">
            <h2 class="message-container-header">MESSAGES</h2>
            <tr>
                <div class="message-container">

                    <div class="flex-box1">
                        <div class="main-container">

                        </div>

                    </div>


                    <footer></footer>
    </body>

    </html>
<?php
} else {
    // Redirect to messages preview if vendor_name or vendor_stall_number is not set
    header("location:admin_messages_preview.php");
}
