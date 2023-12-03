<?php
require("config.php");
$userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $admin_new_password = isset($_POST["admin_new_password"]) ? htmlspecialchars($_POST["admin_new_password"]) : '';
    $admin_confirm_new_password = isset($_POST["admin_confirm_new_password"]) ? htmlspecialchars($_POST["admin_confirm_new_password"]) : '';

    if ($admin_new_password !== $admin_confirm_new_password) {
        echo '<script>';
        echo 'alert("Passwords do not match!");';
        echo 'window.location.href = "admin_forgot_password_2.php";';
        echo '</script>';
        exit();
    }

    $hashedPassword = md5($admin_new_password);

    $password_query = "UPDATE admin_sign_in SET admin_password = ? WHERE admin_userid = ?";
    $stmt = mysqli_prepare($connect, $password_query);

    // Use "ss" for two string parameters
    $stmt->bind_param("ss", $hashedPassword, $userid);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo '<script>';
        echo 'alert("Password Updated!");';
        echo 'window.location.href = "admin_login.php";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Password is the same with the previous!");';
        echo 'window.location.href = "admin_login.php";';
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
            var password = document.getElementsByName("admin_new_password")[0].value;
            var confirmPassword = document.getElementsByName("admin_confirm_new_password")[0].value;
            var messageElement = document.getElementById("passwordMatchMessage");
            var confirmPasswordInput = document.getElementsByName("admin_confirm_new_password")[0];
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
            <label for="Admin User ID">Admin User ID:</label>
            <input type="text" name="admin_userid" value="<?php echo $userid; ?>" required readonly> <br />

            <label for="admin_username">New Password:</label>
            <input type="password" name="admin_new_password" id="admin_new_password" placeholder="8 characters and above" oninput="checkPasswordMatch()"> <br />

            <label for="new_password">Confirm Password:</label>
            <input type="password" name="admin_confirm_new_password" id="admin_confirm_new_password" required oninput="checkPasswordMatch()">
            <span id="passwordMatchMessage"></span><br />

            <input type="submit" value="Update Password" disabled>


        </form>
    </div>

    <div><a href="admin_login.php">Back</a></div>


</body>

</html>