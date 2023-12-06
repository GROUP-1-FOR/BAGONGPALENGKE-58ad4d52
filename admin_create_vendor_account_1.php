<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    //Error message for the first part of the form
    $vendor_first_name_error  = $vendor_last_name_error  = $vendor_mobile_number_error = $vendor_email_error = "";
    //Error message for the second part
    $vendor_userid_error = $vendor_password_error = $vendor_confirm_password_error = "";

    function generateUniqueTransactionId($connect, $vendor_userid)
    {
        // Set the maximum number of attempts to generate a unique ID
        $maxAttempts = 10;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Generate a secure random 6-digit number
            $uniqueId = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Concatenate vendor user ID and unique ID
            $transactionId = $vendor_userid . '-' . $uniqueId;

            // Check if the generated transaction_id already exists in vendor_balance table
            $checkIfExistsVendor = "SELECT transaction_id FROM vendor_balance WHERE transaction_id = '$transactionId'";
            $resultVendor = $connect->query($checkIfExistsVendor);

            // If not exists in vendor_balance table, break the loop
            if ($resultVendor->num_rows === 0) {
                return $transactionId;
            }
        }

        // If maximum attempts are reached, handle the error (e.g., throw an exception)
        throw new Exception("Failed to generate a unique transaction ID after $maxAttempts attempts");
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Part 1 variables
        $vendor_first_name = isset($_POST["vendor_first_name"]) ? htmlspecialchars($_POST["vendor_first_name"]) : '';
        $vendor_last_name = isset($_POST["vendor_last_name"]) ? htmlspecialchars($_POST["vendor_last_name"]) : '';
        $vendor_stall_number = isset($_POST["vendor_stall_number"]) ? trim(htmlspecialchars($_POST["vendor_stall_number"])) : '';
        $vendor_mobile_number = isset($_POST["vendor_mobile_number"]) ? trim(htmlspecialchars($_POST["vendor_mobile_number"])) : '';
        $vendor_email = isset($_POST["vendor_email"]) ? htmlspecialchars($_POST["vendor_email"]) : '';
        $vendor_product_type = isset($_POST["vendor_product"]) ? htmlspecialchars($_POST["vendor_product"]) : '';

        if (!preg_match("/^[a-zA-Z-' ]*$/", $vendor_first_name)) {
            $vendor_first_name_error = "Only letters are allowed";
            $_SESSION['vendor_first_name_error'] = $vendor_first_name_error;
        }

        if (!preg_match("/^[a-zA-Z-' ]*$/", $vendor_last_name)) {
            $vendor_last_name_error = "Only letters are allowed";
            $_SESSION['vendor_last_name_error'] = $vendor_last_name_error;
        }

        if (!is_numeric($vendor_mobile_number)) {
            $vendor_mobile_number_error = "Only numbers are allowed";
            $_SESSION['vendor_mobile_number_error'] = $vendor_mobile_number_error;
        }
        if (!filter_var($vendor_email, FILTER_VALIDATE_EMAIL)) {
            $vendor_email_error = "Wrong email format";
            $_SESSION['vendor_email_error'] = $vendor_email_error;
        }

        if (isset($_SESSION['vendor_first_name_error']) || isset($_SESSION['vendor_last_name_error']) || isset($_SESSION['vendor_mobile_number_error']) || isset($_SESSION['vendor_email_error'])) {
            header("Location: admin_create_vendor_account.php");
            exit();
        }


        // Part2 variables
        $vendor_userid = isset($_POST["vendor_userid"]) ? htmlspecialchars($_POST["vendor_userid"]) : '';
        $vendor_password = isset($_POST["vendor_password"]) ? htmlspecialchars($_POST["vendor_password"]) : '';
        $vendor_confirm_password = isset($_POST["vendor_confirm_password"]) ? htmlspecialchars($_POST["vendor_confirm_password"]) : '';


        if ($vendor_password !== $vendor_confirm_password) {
            echo '<script>';
            echo 'alert("Passwords do not match!");';
            echo 'window.location.href = "admin_create_vendor_account.php";';
            echo '</script>';
            exit();
        }

        // Hash the password
        $hashedVendorPassword = password_hash($vendor_password, PASSWORD_BCRYPT);
        $vendor_full_name = $vendor_first_name . " " . $vendor_last_name;

        // Check if the account already exists
        $checkIfExists = "SELECT * FROM vendor_sign_in WHERE vendor_userid = '$vendor_userid'";
        $resultIfExists = $connect->query($checkIfExists);

        if ($resultIfExists->num_rows > 0) {
            echo '<script>';
            echo 'alert("Account already exists for this user ID!");';
            echo 'window.location.href = "admin_create_vendor_account.php";';
            echo '</script>';
            exit();
        }

        // Call the function to get a unique transaction_id
        $transactionId = generateUniqueTransactionId($connect, $vendor_userid);
        $vendor_transaction_id = $transactionId;

        $_SESSION['vendor_first_name'] = $vendor_first_name;
        $_SESSION['vendor_last_name'] = $vendor_last_name;
        $_SESSION['vendor_full_name'] = $vendor_full_name;
        $_SESSION['vendor_stall_number'] = $vendor_stall_number;
        $_SESSION['vendor_mobile_number'] = $vendor_mobile_number;
        $_SESSION['vendor_product_type'] = $vendor_product_type;
        $_SESSION['vendor_email'] = $vendor_email;
        $_SESSION['vendor_userid'] = $vendor_userid;
        $_SESSION['vendor_hashed_password'] = $hashedVendorPassword;
        $_SESSION['vendor_transaction_id'] = $vendor_transaction_id;

        /*
    // First insertion into vendor_sign_in table
    $sql1 = "INSERT INTO vendor_sign_in (vendor_first_name, vendor_last_name, vendor_name, vendor_stall_number, vendor_mobile_number, vendor_product, vendor_email, vendor_userid, vendor_password) 
    VALUES ('$vendor_first_name', '$vendor_last_name', '$vendor_full_name', '$vendor_stall_number', '$vendor_mobile_number', '$vendor_product_type', '$vendor_email', '$vendor_userid', '$hashedPassword')";

    // Second insertion into vendor_balance table
    $sql2 = "INSERT INTO vendor_balance (vendor_name, vendor_stall_number, vendor_userid, balance, transaction_id) 
    VALUES ('$vendor_full_name', '$vendor_stall_number', '$vendor_userid', '0.00', '$transactionId')";
    if ($connect->query($sql1) === TRUE) {
        if ($connect->query($sql2) === TRUE) {
            // Both insertions successful
            echo '<script>';
            echo 'alert("Vendor Account Created Successfully!");';
            echo 'window.location.href = "admin_index.php";';
            echo '</script>';
        } else {
            // If the second insertion fails, display an error
            echo "Error: " . $sql2 . "<br>" . $connect->error;
        }
    } else {
        // If the first insertion fails, display an error
        echo "Error: " . $sql1 . "<br>" . $connect->error;
    }

    // Close the database connection
    $connect->close();
    */
    }

?>
    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Password Verification</title>
    </head>

    <body>
        <header> LOGO </header>


        <div>
            <h2>Admin Password Before Vendor Account Creation</h2>
        </div>

        <div>
            <form action="admin_create_vendor_account_2.php" method="post">
                <label for="Admin Password Confirmation">Admin Password Confirmation</label>
                <input type="password" name="admin_password" id="admin_password" required>
                <input type="submit" value="Submit">
            </form>

        </div>
        <a href=admin_create_vendor_account.php> CANCEL </a>

        <footer> </footer>
    </body>

    </html>

<?php
} else {
    header("location:admin_login.php");
}
