<?php
// vendor_invoice_summary.php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
    exit();
}

// Initialize variables
$id = $_SESSION["id"];
$userid = $_SESSION["userid"];
$errorMessage = "";

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
        $stmtGetMobileNumber->close();

        if ($vendorMobileNumber == '0' . $inputedNumber) {
            // Redirect to the next step or perform additional actions
            echo '<script>gcashPayOverlay();</script>'; // Add this line to open the overlay
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
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: "Montserrat", sans-serif, Arial, Helvetica, sans-serif !important;
        }

        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        /* #invoice-summary {
            color: #850F16;
            margin-top: 4% !important;
            width: 50%;
            margin: auto;
            border: 3px solid #ccc;
            padding: 20px;
            background-color: #fff;
        } */

        #invoice-summary h2 {
            color: #850F16;
        }

        #invoice-summary p {
            font-size: 1.2em;
            margin: 10px 0;
            color: #850F16;
            text-align: center;
            align-items: center;
            align-content: center;
        }

        .invoice-summary-text {
            width: 360px;
            margin-top: 30px;
            text-align: center;
            align-items: center;
            align-content: center;
            color: maroon;

        }

        #payment-buttons {
            margin-top: 20px;
        }

        #payment-buttons button {
            margin: 10px;
            padding: 10px;
            background-color: #850F16;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Overlay styles */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* semi-transparent black */
            z-index: 999;
            /* ensure it appears above everything else */
        }

        #gcash-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* semi-transparent black */
            z-index: 999;
            /* ensure it appears above everything else */
        }

        #overlay-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            /* ensure it appears above the semi-transparent overlay */
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        #overlay-content p {
            color: #000;
        }

        /* You may need to adjust the styles further based on your design requirements */


        #overlay-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 24px;
        }

        #overlay-content {

            margin-top: 0;
            width: 300px;
        }

        #overlay-holder {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            opacity: .95;
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            background-color: #F4F1EC;
            padding: 40px;
            border-radius: 5px;
            display: flex;
        }

        #overlay-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 520px;
            width: 500px;
            /* background-color: white; */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(to top, white 60%, #1D71FB 50%);
            /* Gradient from blue to white */
            opacity: 1;
            border-radius: 15px;
        }

        .gcash-header {
            margin-top: 10px;
            text-align: left;
            /* Align the text to the left */
            font-size: x-small;
            font-weight: 700;
            color: #6A6A6A;
        }

        .mobile-label {
            padding-top: 10px;
            padding-bottom: 10px;
            text-align: left;
            /* Align the text to the left */
            font-size: x-small;
            color: #6A6A6A;
        }

        .gcash-button {
            width: 90px;
            font-weight: 700;

        }

        .gcb1 {
            margin-top: 10px;
            background-color: #1D71FB;
            border-radius: 20px;
            border-style: none;
            color: white;
            height: 30px;
        }


        .gcb2 {
            margin-top: 15px;
            background-color: #D9D9D9;
            border-color: #1D71FB;
            border-radius: 20px;
            border-style: solid;
            border-width: 1px;
            color: #1D71FB;
            height: 30px;
        }

        .gcash-link {
            color: black;
            margin-top: 130px;
            margin-left: -160px;
            font-size: x-small;
        }

        .reg-link {
            color: #1D71FB;
            text-decoration: underline;
            text-decoration-color: #1D71FB;
        }

        .reg-link:hover {
            color: red;
        }

        .flexbox-row3 {
            font-size: small;
            margin-right: 10px;
        }


        .stall-number {
            display: flex;
            align-items: center;
            padding: 20px;
            width: 1120px;
            height: 70px;
            background-color: maroon;
            margin-top: 10%;
            margin-left: -105%;
        }

        .row3 {
            width: 1120px;
            margin-left: -105%;
            background-color: whitesmoke;
            color: maroon;
            padding: 20px;
            font-weight: 500;
        }

        .total-container {
            width: 900px;
            display: flex;
            text-align: start;
            align-items: end;
            font-weight: 700;
        }
    </style>


    <script>
        function gcashPayOverlay() {
            document.getElementById("gcash-overlay").style.display = "block";
        }

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
            var inputedNumber = document.getElementById("gcash_mobile").value;
            if (!/^9[0-9]{9}$/.test(inputedNumber)) {
                alert("Invalid mobile number format. Please enter a valid 10-digit number starting with 9.");
                return false;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "validate_mobile_number.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.valid) {
                            // If validation is successful, open the GCash overlay
                            gcashPayOverlay();
                        } else {
                            alert("Mobile number does not match. Please try again.");
                        }
                    } else {
                        alert("Mobile number does not match. Please try again.");
                    }
                }
            };
            xhr.send("mobile_number=" + inputedNumber);
            return false;
        }
    </script>

</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>

    <div class="flex-row">
        <div class="faq-table3">
            <div class="flex-box1">
                <div class="main-container">
                    <div id="invoice-summary">
                        <center>
                            <div class="invoice-summary-text">
                                <h1>Invoice Summary</h1>
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
                        <div id="payment-buttons">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <!-- Add hidden input fields for vendor details -->
                                <input type="hidden" name="vendorName" value="<?php echo $vendorName; ?>">
                                <!-- Add other hidden input fields as needed -->
                                <!-- <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>
                                <button onclick="showOverlay()">GCash</button> -->
                            </form>
                            <center>
                                <button type="submit" name="pay_cash" onclick="return confirm('Are you sure you want to pay with cash?')">CASH</button>
                                <button onclick="showOverlay()">GCash</button>
                            </center>
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


            <!-- Overlay -->
            <div id="gcash-overlay">
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