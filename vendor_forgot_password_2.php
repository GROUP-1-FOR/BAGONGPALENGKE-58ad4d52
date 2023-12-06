<?php
require("config.php");
$userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vendor_new_password = isset($_POST["vendor_new_password"]) ? htmlspecialchars($_POST["vendor_new_password"]) : '';
    $vendor_confirm_new_password = isset($_POST["vendor_confirm_new_password"]) ? htmlspecialchars($_POST["vendor_confirm_new_password"]) : '';

    if ($vendor_new_password !== $vendor_confirm_new_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "vendor_forgot_password_2.php";';
        echo '</script>';
        exit();
    }

    $hashedPassword = password_hash($vendor_new_password, PASSWORD_BCRYPT);

    $password_query = "UPDATE vendor_sign_in SET vendor_password = ? WHERE vendor_userid = ?";
    $stmt = mysqli_prepare($connect, $password_query);

    // Use "ss" for two string parameters
    $stmt->bind_param("ss", $hashedPassword, $userid);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo '<script>';
        echo 'alert("Password Updated!");';
        echo 'window.location.href = "vendor_login.php";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Failed to update Password!");';
        echo 'window.location.href = "vendor_login.php";';
        echo '</script>';
        exit();
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <script>
        function checkPasswordMatch() {
            var password = document.getElementsByName("vendor_new_password")[0].value;
            var confirmPassword = document.getElementsByName("vendor_confirm_new_password")[0].value;
            var messageElement = document.getElementById("passwordMatchMessage");
            var confirmPasswordInput = document.getElementsByName("vendor_confirm_new_password")[0];
            var submitButton = document.querySelector('input[type="submit"]');

            // Enable or disable Confirm Password based on whether Password is empty
            confirmPasswordInput.disabled = password.length === 0;

            // Check if the "Password" field is not empty
            if (password.length > 0) {
                // Check if the "Confirm Password" field is also not empty
                if (confirmPassword.length > 0) {
                    // Check if passwords match
                    if (password === confirmPassword) {
                        // Check if passwords have at least 8 characters
                        if (password.length >= 8 && confirmPassword.length >= 8) {
                            messageElement.innerHTML = "Passwords match and meet the minimum length requirement.";
                            messageElement.style.color = "green";
                            submitButton.disabled = false; // Enable the button
                        } else {
                            messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                            messageElement.style.color = "red";
                            submitButton.disabled = true; // Enable the button
                        }
                    } else {
                        messageElement.innerHTML = "Passwords do not match.";
                        messageElement.style.color = "red";
                        submitButton.disabled = true; // Enable the button
                    }
                } else {
                    // "Confirm Password" field is empty, clear the message
                    messageElement.innerHTML = "";
                }
            } else {
                // "Password" field is empty, clear the message and "Confirm Password" field
                messageElement.innerHTML = "";
                confirmPasswordInput.value = "";
            }

            return password === confirmPassword && password.length >= 8 && confirmPassword.length >= 8;
        }
    </script>
</head>

<body>
    <div>
        <h1>New Password</h1><br />
        <form action="" method="post" onsubmit="return confirm('Proceed?');">
            <label for="Vendor User ID">Vendor User ID:</label>
            <input type="text" name="vendor_userid" value="<?php echo $userid; ?>" required readonly> <br />

            <label for="vendor_username">New Password:</label>
            <input type="password" name="vendor_new_password" id="vendor_new_password" placeholder="8 characters and above" oninput="checkPasswordMatch()"> <br />

            <label for="new_password">Confirm Password:</label>
            <input type="password" name="vendor_confirm_new_password" id="vendor_confirm_new_password" required oninput="checkPasswordMatch()">
            <span id="passwordMatchMessage"></span><br />

            <input type="submit" value="Update Password" disabled>

        </form>
    </div>

    <div><a href="vendor_login.php">Back</a></div>


</body>

</html>