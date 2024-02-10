<?php
// vendor_gcash.php

// Include the configuration file
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}

if (isset($_POST['gcash_mobile'])) {
    $inputedNumber = $_POST['gcash_mobile'];

    // Validate the mobile number format
    if (preg_match('/^9[0-9]{9}$/', $inputedNumber)) {
        // Check if the next button is clicked
        if (isset($_POST['next_button'])) {
            // Check if the mobile number matches the one in the vendor_sign_in table
            $sqlCheckMobile = "SELECT vendor_mobile_number FROM vendor_sign_in WHERE vendor_userid = ?";
            $stmtCheckMobile = $connect->prepare($sqlCheckMobile);
            $stmtCheckMobile->bind_param('s', $userid); // Change $vendorUserId to $userid
            $stmtCheckMobile->execute();
            $stmtCheckMobile->bind_result($vendorMobileNumber);
            $stmtCheckMobile->fetch();
            $stmtCheckMobile->close();

            $checkinputedNumber = "0" . $inputedNumber;

            // If the mobile number matches, proceed to the next step
            if ($vendorMobileNumber == $checkinputedNumber) {
                // Redirect to the next step or perform additional actions
                header("Location: gcash_pay.php"); // Replace with the actual next step file
                exit();
            } else {
                // Display an error message
                $errorMessage = "Mobile number does not match. Please try again.";
            }
        }
    } else {
        // Display an error message for an invalid mobile number format
        $errorMessage = "Invalid mobile number format. Please enter a valid 10-digit number starting with 9.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>GCash Payment</title>
    <script>
        function validateVendorMobileNumber() {
            var inputElement = document.getElementById("gcash_mobile");
            var vendor_mobile_number = inputElement.value;

            // Replace non-numeric characters with an empty string
            inputElement.value = vendor_mobile_number.replace(/[^0-9]/g, '');
        }
    </script>
    <!-- Add your styles here -->
</head>

<body>
    <div id="gcash-form">
        <h2>Login to pay with GCash</h2>
        <?php
        if (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
        ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label>Mobile Number</label>
            <input type="text" name="gcash_mobile" value="+63" readonly style="width: 40px;">
            <input type="text" name="gcash_mobile" id="gcash_mobile" placeholder="XXXXXXXXXX" maxlength="10" oninput="validateVendorMobileNumber();" required>
            <br>
            <button type="submit" name="next_button">Next</button>
            <button type="button" onclick="window.location.href='vendor_invoice_summary.php'">Cancel</button>
        </form>
    </div>
</body>

</html>