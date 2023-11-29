<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Fetch admin_name from admin_sign_in based on admin_id
    $adminNameQuery = "SELECT admin_name FROM admin_sign_in WHERE admin_id = '$admin_id'";
    $adminNameResult = mysqli_query($connect, $adminNameQuery);

    // Check if the query was successful and data is present
    if ($adminNameResult && mysqli_num_rows($adminNameResult) > 0) {
        $adminRow = mysqli_fetch_assoc($adminNameResult);
        $sender_admin = $adminRow['admin_name'];
    } else {
        // Handle the case where the admin_name is not found
        // You can redirect to an error page or display an error message
        echo "Error: Unable to fetch admin_name.";
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle the form submission to send a message
        if (isset($_POST["receiver"]) && isset($_POST["message"])) {
            $receiver = $_POST["receiver"];
            $message = $_POST["message"];
            $timestamp = date("Y-m-d H:i:s");   

            // Fetch the vendor name based on the selected receiver
            $vendorNameQuery = "SELECT vendor_name FROM vendor_sign_in WHERE vendor_name = '$receiver'";
            $vendorNameResult = mysqli_query($connect, $vendorNameQuery);

            if ($vendorNameResult && mysqli_num_rows($vendorNameResult) > 0) {
                $vendorRow = mysqli_fetch_assoc($vendorNameResult);
                $receiver_vendor = $vendorRow['vendor_name'];

                // Insert the message into the database
                $insertQuery = "INSERT INTO messages (sender_admin, receiver_vendor, message, timestamp) 
                VALUES ('$sender_admin', '$receiver_vendor', '$message', '$timestamp')";
                // Assuming your database connection variable is $connect
                mysqli_query($connect, $insertQuery);

                // Redirect to avoid form resubmission
                header("Location: {$_SERVER['PHP_SELF']}");
                exit;
            } else {
                // Handle the case where the receiver vendor name is not found
                // You can redirect to an error page or display an error message
                echo "Error: Unable to fetch receiver vendor name.";
                exit;
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        header {
            background-color: #3498db;
            padding: 10px;
            text-align: center;
        }

        section {
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 8px;
        }

        select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        p {
            background-color: #f2f2f2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #3498db;
            margin-top: 20px;
        }

        a:hover {
            color: #2980b9;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo $sender_admin?>!</h1>
    </header>
    <h2>Messages:</h2>
        <?php
        // Display messages from the database
        $selectQuery = "SELECT * FROM messages WHERE sender_admin = '$sender_admin' OR receiver_vendor = '$sender_admin' ORDER BY timestamp DESC";
        $result = mysqli_query($connect, $selectQuery);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p>From: " . $row['sender_admin'] . "<br>To: " . $row['receiver_vendor'] . "<br>Message: " . $row['message'] . "<br>Timestamp: " . $row['timestamp'] . "</p>";
        }
        ?>
    <section>
        <h2>Send a Message:</h2>
        <form method="post" action="">
            <label for="receiver">Receiver Vendor:</label>
            <select name="receiver" required>
                <?php
                // Populate dropdown with vendor names from vendor_sign_in table
                $vendorQuery = "SELECT vendor_name FROM vendor_sign_in";
                $vendorResult = mysqli_query($connect, $vendorQuery);

                while ($vendorRow = mysqli_fetch_assoc($vendorResult)) {
                    echo "<option value='" . $vendorRow['vendor_name'] . "'>" . $vendorRow['vendor_name'] . "</option>";
                }
                ?>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" required></textarea>

            <button type="submit">Send Message</button>
        </form>

        
    </section>

    <a href="admin_index.php">
        <h1>BACK</h1>
    </a>
</body>

</html>

<?php
} else {
    header("location:admin_login.php");
}
?>