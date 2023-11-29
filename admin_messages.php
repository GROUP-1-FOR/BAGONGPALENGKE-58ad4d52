<?php
require("config.php");

$admin_name = ''; // Initialize the variable

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Retrieve admin_name from admin_sign_in table
    $adminQuery = "SELECT admin_name FROM admin_sign_in WHERE admin_id = '$admin_id'";
    // Execute the query and fetch the result
    $adminResult = mysqli_query($connect, $adminQuery); // Assuming $connect is your database connection

    if ($adminResult) {
        $adminRow = mysqli_fetch_assoc($adminResult);
        $admin_name = $adminRow['admin_name'];
    } else {
        // Handle the error or redirect as needed
        die("Error fetching admin details");
    }

    // Retrieve vendor names from vendor_sign_in table for the dropdown
    $vendorQuery = "SELECT vendor_name FROM vendor_sign_in";
    // Execute the query and fetch the results
    $vendorResult = mysqli_query($connect, $vendorQuery); // Assuming $connect is your database connection

    if (!$vendorResult) {
        // Handle the error or redirect as needed
        die("Error fetching vendor details");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle message sending
        $receiver_vendor = $_POST["receiver_vendor"];
        $message_content = $_POST["message_content"];
    
        // Perform validation if needed
    
        // Insert the message into the database
        $query = "INSERT INTO messages (sender_admin, sender_vendor, receiver_admin, receiver_vendor, message) 
                  VALUES ('$admin_name', '', '', '$receiver_vendor', '$message_content')";
        // Execute the query, you may want to use prepared statements for security
        // $result = mysqli_query($connect, $query);
    
        // Redirect to avoid form resubmission on page refresh
        header("Location: admin_messages.php");
        exit();
    }

    // Include necessary HTML and form for sending messages
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messages</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 18px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo $admin_name; ?>!</h1>
    </header>

    <form method="post" action="">
        <label for="receiver_vendor">Receiver Vendor:</label>
        <select name="receiver_vendor" id="receiver_vendor" required>
            <option value="" disabled selected>Select a vendor</option>
            <?php
            while ($row = mysqli_fetch_assoc($vendorResult)) {
                echo "<option value='{$row['vendor_name']}'>{$row['vendor_name']}</option>";
            }
            ?>
        </select>

        <label for="message_content">Message:</label>
        <textarea name="message_content" id="message_content" placeholder="Enter your message" required></textarea>

        <input type="submit" value="Send Message">
    </form>

    <a href="admin_main.php">Go back to Main Page</a>
</body>

</html>
<?php
} else {
    header("location:admin_login.php");
}
?>