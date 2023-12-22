<?php
require("config.php");
$admin_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$admin_token_error = "";

if (isset($_POST["admin_token"])) {
    $entered_token = htmlspecialchars($_POST["admin_token"]);

    $select_query = "SELECT admin_token FROM admin_sign_in WHERE admin_email = '$admin_email'";
    $result = mysqli_query($connect, $select_query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $stored_token = $row["admin_token"];

        // Check if entered OTP matches the stored OTP
        if ($entered_token == $stored_token) {
            echo '<script>';
            echo 'alert("Token Verified!");';
            echo 'window.location.href = "admin_forgot_password_2.php?email=' . urlencode($admin_email) . '";';
            echo '</script>';
        } else {
            $admin_token_error = "Wrong Token";
        }
    } else {
        // Error retrieving OTP and trials from the database
        echo "Error: " . mysqli_error($connect);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>
        Reset Password Token Verification
    </title>
</head>

<body>
    <div align="center">
        <div>
            <form action="" method="post">
                <input class="otp-box" type="text" maxlength="6" id="otp" name="admin_token" title="Please enter six characters" placeholder="Enter Token" required>
                <button class="submit-button" type="submit">Verify Token</button> <br />
                <span style="color: red">
                    <?php echo $admin_token_error; ?>
                </span>
            </form>
        </div>

        <a href=admin_forgot_password.php>
            <h1>Back</h1>
        </a>
    </div>
</body>

</html>