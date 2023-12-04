<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    $sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$userid'";

    // Execute the query
    $result = $connect->query($sql);
    $admin_name = "";
    $admin_name_error = "";

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            $admin_name = $row['admin_name'];
        }
    } else {
        $admin_name_error = "No results found for user ID $userId";
    }


?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Email OTP Simulation</title>
    </head>

    <body>
        <div>
            <h1 align="center">GMAIL ACCOUNT SIMULATION</h1>
            <h2 align="center">Remember your OTP, <?php echo $admin_name  ?>! </h2> <br />
            <span style="color: red;"> <?php echo $admin_name_error; ?></span>
        </div>

        <div>
            <h1 style="color: green;" align="center">
                <?php
                if (isset($_SESSION['admin_otp_message'])) {
                    echo $_SESSION['admin_otp_message'];
                }
                ?>
            </h1>
        </div>

        <a href=admin_otp_verification.php>
            <h2 align="center">Proceed</h2>
        </a>

        <a href=admin_logout.php>
            <h5 align="right">LOGOUT</h5>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_login.php");
}
