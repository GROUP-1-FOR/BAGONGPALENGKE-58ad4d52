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
        echo 'alert("Failed to update Password!");';
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
</head>

<body>
    <div>
        <h1>New Password</h1><br />
        <form action="" method="post">
            <label for="Admin User ID">Admin User ID:</label>
            <input type="text" name="admin_userid" value="<?php echo $userid; ?>" required readonly> <br />

            <label for="admin_username">New Password:</label>
            <input type="password" name="admin_new_password" required> <br />

            <label for="new_password">Confirm Password:</label>
            <input type="password" name="admin_confirm_new_password" required oninput="checkPasswordMatch()">
            <span id="passwordMatchMessage"></span><br />

            <input type="submit" value="Update Password" disabled>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    checkPasswordMatch(); // Initial check
                });

                function checkPasswordMatch() {
                    var password = document.getElementsByName("admin_new_password")[0].value;
                    var confirmPassword = document.getElementsByName("admin_confirm_new_password")[0].value;
                    var messageElement = document.getElementById("passwordMatchMessage");
                    var submitButton = document.querySelector('input[type="submit"]');

                    if (password === confirmPassword) {
                        messageElement.innerHTML = "Passwords match";
                        messageElement.style.color = "green";
                        submitButton.disabled = false; // Enable the button
                    } else {
                        messageElement.innerHTML = "Passwords do not match";
                        messageElement.style.color = "red";
                        submitButton.disabled = true; // Disable the button
                    }
                }
            </script>
        </form>
    </div>

    <div><a href="admin_login.php">Back</a></div>


</body>

</html>