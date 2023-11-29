<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Fetch the latest message from each vendor
    $sqlFetchLatestMessages = "SELECT vendor_name, timestamp, vendor_messages FROM system_messages WHERE (vendor_name, timestamp) IN (SELECT vendor_name, MAX(timestamp) AS latest_timestamp FROM system_messages GROUP BY vendor_name)";
    $resultLatestMessages = $connect->query($sqlFetchLatestMessages);

    // Display the latest messages
    ?>
 <!DOCTYPE html>
<html>

<head>
    <title>Messages Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2,
        h3 {
            color: #333;
        }

        div.message-box {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome, <?php echo $admin_userid ?>!</h1>
        <h2>Messages Preview</h2>

        <?php
        if ($resultLatestMessages) {
            if ($resultLatestMessages->num_rows > 0) {
                while ($rowLatestMessage = $resultLatestMessages->fetch_assoc()) {
                    $vendor_name = $rowLatestMessage['vendor_name'];
                    $latest_timestamp = $rowLatestMessage['timestamp'];
                    $latest_message = $rowLatestMessage['vendor_messages'];
        ?>
                    <div class="message-box">
                        <h3>Vendor: <?php echo $vendor_name; ?></h3>
                        <p>Latest Message: <?php echo $latest_message; ?></p>
                        <p>Timestamp: <?php echo $latest_timestamp; ?></p>
                    </div>
        <?php
                }
            } else {
                echo "<p>No messages available.</p>";
            }
        } else {
            echo "Error fetching messages: " . $connect->error;
        }
        ?>

        <a href="admin_index.php">
            <h1>Back</h1>
        </a>

    </div>
</body>

</html>
<?php
} else {
    header("location: admin_login.php");
}
?>
