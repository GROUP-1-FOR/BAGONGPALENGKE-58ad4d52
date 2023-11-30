<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Fetch the latest message (vendor_messages or admin_reply) from each recipient,
    // ordered by the latest timestamp in descending order
    $sqlFetchLatestMessages = "
        SELECT
            recipient_name,
            MAX(CASE WHEN admin_reply IS NULL THEN vendor_timestamp ELSE admin_timestamp END) AS latest_timestamp,
            MAX(CASE WHEN admin_reply IS NULL THEN vendor_messages ELSE admin_reply END) AS latest_message
        FROM
            system_messages
        WHERE
            recipient_name IN (SELECT DISTINCT vendor_name FROM system_messages WHERE vendor_name IS NOT NULL
                UNION
                SELECT DISTINCT admin_name FROM system_messages WHERE admin_name IS NOT NULL)
        GROUP BY
            recipient_name
        ORDER BY
            latest_timestamp DESC
    ";
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
                        $recipient_name = $rowLatestMessage['recipient_name'];
                        $latest_timestamp = $rowLatestMessage['latest_timestamp'];
                        $latest_message = $rowLatestMessage['latest_message'];
            ?>
                        <a href="admin_messages.php?recipient=<?php echo urlencode($recipient_name); ?>">
                            <div class="message-box">
                                <h3>Recipient: <?php echo $recipient_name; ?></h3>
                                <p>Latest Message: <?php echo $latest_message; ?></p>
                                <p>Timestamp: <?php echo $latest_timestamp; ?></p>
                            </div>
                        </a>
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
