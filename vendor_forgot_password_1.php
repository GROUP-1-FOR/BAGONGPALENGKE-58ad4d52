<?php
require("config.php");

function isTokenValid($token)
{
    // Split the token into the actual token and the expiration timestamp
    $tokenParts = explode('|', $token);

    if (count($tokenParts) === 2) {
        // Extract the expiration timestamp
        $expirationTimestamp = $tokenParts[1];

        // Check if the token is still within the expiration time
        return time() <= $expirationTimestamp;
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = htmlspecialchars($_POST["vendor_userid"]);

    $query = "SELECT vendor_token FROM vendor_sign_in WHERE vendor_userid = '$userid'";
    // Perform the query
    $result = mysqli_query($connect, $query);

    if ($result) {
        // Fetch the user data as an associative array
        $vendor_token = mysqli_fetch_assoc($result);

        if ($vendor_token && isTokenValid($vendor_token['vendor_token'])) {

            header("Location: vendor_forgot_password_2.php?userid=" . urlencode($userid));
            exit();
        } else {

            echo '<script>';
            echo 'alert("Token Expired!");';
            echo 'window.location.href = "vendor_login.php";';
            echo '</script>';
        }
    } else {
        // Handle the query error
        echo "Error: " . mysqli_error($connect);
        echo '<script>';
        echo 'alert("Wrong Vendor User ID!");';
        echo 'window.location.href = "vendor_login.php";';
        echo '</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>

<body>
    <div align="center">
        <div>
            <h2>REENTER VENDOR USER ID</h2>
            <form action="" onsubmit="return confirm('Proceed?');" method="post">
                <label for="Vendor User ID">Vendor User ID:</label>
                <input type="text" name="vendor_userid" value="<?php echo $userid = isset($_GET['userid']) ? htmlspecialchars($_GET['userid']) : ''; ?>" required readonly> <br />
                <input type="submit" value="Enter"><br />
            </form>
        </div>
        <script>
            // Testing the confirm function
            // alert(confirm('Testing confirm function. Proceed?'));
        </script>

        <div>
            <a href=vendor_login.php>
                <h2>Back</h2> <br />
            </a>
        </div>
    </div>
</body>

</html>