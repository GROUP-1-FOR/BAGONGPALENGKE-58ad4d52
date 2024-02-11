<?php
// vendor_invoice_summary.php

// Include the configuration file
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
    exit();
}

// Check if the necessary POST data is set, including the transaction_id
if (
    isset($_POST['vendorName']) &&
    isset($_POST['vendorUserId']) &&
    isset($_POST['vendorStallNumber']) &&
    isset($_POST['balance']) &&
    isset($_POST['transactionId'])
) {
    // Get data from POST variables, including transaction_id
    $vendorName = $_POST['vendorName'];
    $vendorUserId = $_POST['vendorUserId'];
    $vendorStallNumber = $_POST['vendorStallNumber'];
    $balance = $_POST['balance'];
    $transactionId = $_POST['transactionId'];
} else {
    // Redirect to the main page if data is not set
    header("Location: vendor_index.php");
    exit();
}

// Check if the "Cash" button is clicked

if (isset($_POST['pay_cash'])) {
    // Insert payment data into ven_payments table
    $paymentDate = date('Y-m-d H:i:s');
    $paymentMethod = "CASH"; // Set the payment method to "CASH"

    $sqlInsertPayment = "INSERT INTO ven_payments (vendor_userid, vendor_name, balance, archived, confirmed, payment_date, mop, transaction_id) VALUES (?, ?, ?, 0, 0, ?, ?, ?)";
    $stmtInsertPayment = $connect->prepare($sqlInsertPayment);
    $stmtInsertPayment->bind_param('ssdsss', $vendorUserId, $vendorName, $balance, $paymentDate, $paymentMethod, $transactionId); // Use 's' for VARCHAR, 'd' for DOUBLE
    $stmtInsertPayment->execute();

    // Insert into admin_notification table
    $notifTitle = "Payment Confirmation Request";
    $confirmValue = 1; // Set the confirm value to 1

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Logic for handling GCash payment
if (isset($_POST['gcash_mobile'])) {
    $inputedNumber = $_POST['gcash_mobile'];

    // Validate the mobile number format
    if (preg_match('/^9[0-9]{9}$/', $inputedNumber)) {
        // Retrieve the mobile number from the database
        $sqlGetMobileNumber = "SELECT vendor_mobile_number FROM vendor_sign_in WHERE vendor_userid = ?";
        $stmtGetMobileNumber = $connect->prepare($sqlGetMobileNumber);
        $stmtGetMobileNumber->bind_param('s', $userid);
        $stmtGetMobileNumber->execute();
        $stmtGetMobileNumber->bind_result($vendorMobileNumber);
        $stmtGetMobileNumber->fetch();
        //$stmtGetMobileNumber->close();

        // Check if the inputted mobile number matches the one in the database
        if ($vendorMobileNumber == '0' . $inputedNumber) {
            // Redirect to the next step or perform additional actions
            header("Location: gcash_pay.php"); // Replace with the actual next step file
            exit();
        } else {
            // Display an error message
            $errorMessage = "Mobile number does not match. Please try again.";
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
    <title>Invoice Summary</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="overlay.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


    <script>
        function showOverlay() {
            document.getElementById("overlay").style.display = "block";
        }

        function hideOverlay() {
            document.getElementById("overlay").style.display = "none";
        }

        function validateVendorMobileNumber() {
            var inputElement = document.getElementById("gcash_mobile");
            var vendor_mobile_number = inputElement.value;

            // Replace non-numeric characters with an empty string
            inputElement.value = vendor_mobile_number.replace(/[^0-9]/g, '');
        }

        function submitGcashForm() {
            // Validate the mobile number format
            var inputedNumber = document.getElementById("gcash_mobile").value;
            if (!/^9[0-9]{9}$/.test(inputedNumber)) {
                alert("Invalid mobile number format. Please enter a valid 10-digit number starting with 9.");
                return false; // Prevent form submission
            }

            // Submit the form
            document.getElementById("gcash_form").submit();
            return true;
        }

        // Modified hideOverlay function to prevent automatic closing on error
        function hideOverlayOnError() {
            var errorMessage = "<?php echo isset($errorMessage) ? $errorMessage : ''; ?>";
            if (errorMessage.trim() === "") {
                showOverlay();
            }
        }
    </script>

</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>

    <div class="flex-row">
        <h1 class="manage-account-header">INVOICE SUMMARY</h1>
        <div class="faq-table3">
            <div class="flex-box1">
                <div class="main-container">
                    <div id="invoice-summary">
                        <center>
                            <div class="invoice-summary-text">
             
                                <p><strong style="color: maroon;">Vendor Name: </strong><?php echo $vendorName; ?></p>
                                <p><strong style="color: maroon;">Vendor ID: </strong><?php echo $vendorUserId; ?></p>
                                <p><strong style="color: maroon;">Payment Status: </strong>To be paid</p>
                                <p><strong style="color: maroon;">Transaction ID: </strong><?php echo $transactionId; ?></p>
                                <div class="stall-number">
                                    <p style="color: white;"><strong>Stall Number: </strong> <?php echo $vendorStallNumber; ?></p>
                                </div>
                                <div class="flexbox-row3 row3">
                                    <div class="total-container">
                                        <p>Total: </p>
                                    </div>
                                    <div>
                                        <strong>
                                            <p> Amount: </p>
                                        </strong>
                                        <p><strong>Php </strong><?php echo number_format($balance, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        </center>
                        <!-- <div id="payment-buttons"> -->
                        <!-- <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> -->
                        <!-- Add hidden input fields for vendor details -->
                        <!-- <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>"> -->
                        <!-- Add other hidden input fields as needed -->
                        <!-- <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>
                                <button onclick="showOverlay()">GCash</button> -->
                        <!-- <center>
                                    <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>
                                    <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>

                                </center> -->
                        <!-- </form> -->

                        <!-- </div> -->


                        <div id="payment-buttons">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <!-- Add hidden input fields for vendor details -->
                                <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                                <input type="hidden" name="vendorUserId" value="<?php echo $vendorUserId; ?>">
                                <input type="hidden" name="vendorStallNumber" value="<?php echo $vendorStallNumber; ?>">
                                <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                                <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>">
                                <center>
                                    <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>
                                </center>
                            </form>
                            <button onclick="showOverlay()">Pay with GCash</button>
                        </div>

                    </div>







                    <p><a class="back-button2" href="vendor_index.php">Back</a></p>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div id="overlay">
            <div id="overlay-container">
                <div>
                    <div id="overlay-holder">
                        <div id="overlay-content">
                            <p class="gcash-header">Login to pay with GCash</p>
                            <form id="gcash_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                                <input type="hidden" name="vendorUserId" value="<?php echo $vendorUserId; ?>">
                                <input type="hidden" name="vendorStallNumber" value="<?php echo $vendorStallNumber; ?>">
                                <input type="hidden" name="balance" value="<?php echo $balance; ?>">
                                <input type="hidden" name="transactionId" value="<?php echo $transactionId; ?>">

                                <div class="flexbox-column">
                                    <label class="mobile-label">Mobile Number</label>
                                    <div class="flexbox-row3">
                                        <p class=""> +63</p>
                                        <input class="input-box1" type="text" name="gcash_mobile" id="gcash_mobile" placeholder="XXXXXXXXXX" maxlength="10" oninput="validateVendorMobileNumber();" required>
                                    </div>
                                    <?php if (isset($errorMessage)) {
                                        echo "<p class='error-message3'>$errorMessage</p>";
                                    } ?>
                                </div>
                                <br>
                                <div class="flexbox-column-center ">
                                    <button class="gcash-button gcb1" type="button" onclick="return submitGcashForm()">Next</button>
                                    <button class="gcash-button gcb2" type="button" onclick="hideOverlay()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <img class="gcash-logo" src="assets\images\sign-in\gcash-logo.png" alt="GCash Logo">
                    <p class="gcash-link"> Don’t have a GCash account? <a class="reg-link" href="https://m.gcash.com/gcashapp/gcash-promotion-web/2.0.0/index.html#/?referralCode=hWlkIm1"> Register now?</a> </p>
                </div>
            </div>
        </div>

    </div>
    </div>

    <br>
    <br>
    <footer></footer>
</body>

</html>