<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    $sql = "SELECT vendor_name FROM vendor_sign_in WHERE vendor_userid = '$userid'";
    // Execute the query
    $result = $connect->query($sql);
    $vendor_name = "";
    $vendor_name_error = "";

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            $vendor_name = $row['vendor_name'];
        }
    } else {
        $vendor_name_error = "No results found for user ID $userId";
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
            <h2 align="center">Remember your OTP, <?php echo $vendor_name  ?>! </h2> <br />
            <span style="color: red;"> <?php echo $vendor_name_error; ?></span>
        </div>

        <div>
            <h1 style="color: green;" align="center">
                <?php
                if (isset($_SESSION['vendor_otp_message'])) {
                    echo $_SESSION['vendor_otp_message'];
                }
                ?>
            </h1>
        </div>


        <a href=vendor_otp_verification.php>
            <h2 align="center">Proceed</h2>
        </a>

        <a href=vendor_logout.php>
            <h5 align="right">LOGOUT</h5>
        </a>
    </body>


    </html>
<?php } else {
    header("location:vendor_logout.php");
}
